<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Imagick;

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
     * @var \Imagick
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

    public function __construct(Image $image, \Imagick $resource)
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
                $this->resource->setIteratorIndex($offset);
                $this->resource->setImage($image);
            } catch (\ImagickException $e) {
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
        try {
            $coalescedResource = $this->resource->coalesceImages();
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Failed to coalesce layers', $e->getCode(), $e
            );
        }

        $count = $coalescedResource->getNumberImages();
        for ($offset = 0; $offset < $count; $offset++) {
            try {
                $coalescedResource->setIteratorIndex($offset);
                $this->layers[$offset] = $coalescedResource->getImage();
            } catch (\ImagickException $e) {
                throw new RuntimeException(
                    'Failed to retrieve layer', $e->getCode(), $e
                );
            }
        }
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
     * @return \Imagick
     * @throws RuntimeException
     */
    private function extractAt($offset)
    {
        if (!isset($this->layers[$offset])) {
            try {
                $this->resource->setIteratorIndex($offset);
                $this->layers[$offset] = $this->resource->getImage();
            } catch (\ImagickException $e) {
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
            return $this->resource->getNumberImages();
        } catch (\ImagickException $e) {
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

        if ($value instanceof \Imagick) {
            $frame = $value;
        } elseif (file_exists($value) && is_file($value)) {
            $frame = new \Imagick($value);
        } else {
            throw new InvalidArgumentException(
                'Invalid argument, a new layer must be an Imagick instance or a filepath'
            );
        }

        $this->resource->setIteratorIndex($offset);
        $this->resource->addImage($frame);

        $this->resource->setIteratorIndex($currentOffset);
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

        $this->resource->setIteratorIndex($offset);
        $this->resource->removeImage();
    }
}
