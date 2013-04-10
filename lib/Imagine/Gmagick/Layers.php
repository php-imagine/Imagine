<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gmagick;

use Imagine\Image\LayersInterface;
use Imagine\Exception\RuntimeException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\InvalidArgumentException;

class Layers implements LayersInterface
{
    /**
     * @var Image
     */
    private $image;
    /**
     * @var \Gmagick
     */
    private $resource;
    /**
     * @var integer
     */
    private $offset = 0;
    /**
     * @var array
     */
    private $layers = array();

    public function __construct(Image $image, \Gmagick $resource)
    {
        $this->image = $image;
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function merge()
    {
        foreach ($this->layers as $offset => $image) {
            try {
                $this->resource->setimageindex($offset);
                $this->resource->setimage($image);
            } catch (\GmagickException $e) {
                throw new RuntimeException(
                    'Failed to substitute layer', $e->getCode(), $e
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function coalesce()
    {
        throw new RuntimeException("Gmagick does not support coalescing");
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return new Image($this->extractAt($this->offset));
    }

    /**
     *
     * @param integer $offset
     * @return \Gmagick
     * @throws RuntimeException
     */
    private function extractAt($offset)
    {
        if (!isset($this->layers[$offset])) {
            try {
                $this->resource->setimageindex($offset);
                $this->layers[$offset] = $this->resource->getimage();
            } catch (\GmagickException $e) {
                throw new RuntimeException(
                    sprintf('Failed to extract layer %d', $offset),
                    $e->getCode(), $e
                );
            }
        }

        return $this->layers[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->offset;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->offset;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->offset = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->offset < count($this);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        try {
            return $this->resource->getnumberimages();
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Failed to count the number of layers', $e->getCode(), $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return is_int($offset) && $offset >= 0 && $offset < count($this);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return new Image($this->extractAt($offset));
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $currentOffset = $this->offset;

        if (null === $offset) {
            $offset = count($this) - 1;
        } else {
            if (!is_int($offset)) {
                throw new InvalidArgumentException(
                    'Invalid offset for layer, it must be an integer'
                );
            }

            if (count($this) < $offset || 0 > $offset) {
                throw new OutOfBoundsException(
                    'Invalid offset for layer, it must be a value between 0 and %d',
                    count($this)
                );
            }

            if (isset($this[$offset])) {
                unset($this[$offset]);
                $offset = $offset - 1;
            }
        }

        if ($value instanceof \Gmagick) {
            $frame = $value;
        } elseif (file_exists($value) && is_file($value)) {
            $frame = new \Gmagick($value);
        } else {
            throw new InvalidArgumentException(
                'Invalid argument, a new layer must be an Gmagick instance or a filepath'
            );
        }

        $this->resource->setimageindex($offset);
        $this->resource->nextimage();
        $this->resource->addimage($frame);

        /**
         * ugly hack to bypass issue https://bugs.php.net/bug.php?id=64623
         */
        if (count($this) == 2) {
            $this->resource->setimageindex($offset+1);
            $this->resource->nextimage();
            $this->resource->addimage($frame);
            unset($this[0]);
        }

        $this->resource->setimageindex($currentOffset);
        $this->layers = array();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        try {
            $this->extractAt($offset);
        } catch (RuntimeException $e) {
            return;
        }

        $this->resource->setimageindex($offset);
        $this->resource->removeimage();
    }
}
