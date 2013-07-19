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

use Imagine\Image\AbstractLayers;
use Imagine\Imagick\Image;
use Imagine\Exception\RuntimeException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Image\Palette\PaletteInterface;

class Layers extends AbstractLayers
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

    private $palette;

    public function __construct(Image $image, PaletteInterface $palette, \Imagick $resource)
    {
        $this->image = $image;
        $this->resource = $resource;
        $this->palette = $palette;
    }

    /**
     * {@inheritdoc}
     */
    public function merge()
    {
        foreach ($this->layers as $offset => $image) {
            try {
                $this->resource->setIteratorIndex($offset);
                $this->resource->setImage($image->getImagick());
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
    public function animate($format, $delay, $loops)
    {
        if ('gif' !== strtolower($format)) {
            throw new InvalidArgumentException('Animated picture is currently only supported on gif');
        }

        foreach (array('Loops' => $loops, 'Delay' => $delay) as $name => $value) {
            if (!is_int($value) || $value < 0) {
                throw new InvalidArgumentException(sprintf('%s must be a positive integer.', $name));
            }
        }

        try {
            foreach ($this as $offset => $layer) {
                $this->resource->setIteratorIndex($offset);
                $this->resource->setFormat($format);
                $this->resource->setImageDelay($delay / 10);
                $this->resource->setImageTicksPerSecond(100);
                $this->resource->setImageIterations($loops);
            }
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to animate layers', $e->getCode(), $e);
        }

        return $this;
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
                $this->layers[$offset] = new Image($coalescedResource->getImage(), $this->palette);
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
        return $this->extractAt($this->offset);
    }

    /**
     * Tries to extract layer at given offset
     *
     * @param  integer          $offset
     * @return Image
     * @throws RuntimeException
     */
    private function extractAt($offset)
    {
        if (!isset($this->layers[$offset])) {
            try {
                $this->resource->setIteratorIndex($offset);
                $this->layers[$offset] = new Image($this->resource->getImage(), $this->palette);
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
        return $this->extractAt($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $image)
    {
        if (!$image instanceof Image) {
            throw new InvalidArgumentException('Only an Imagick Image can be used as layer');
        }

        if (null === $offset) {
            $offset = count($this) - 1;
        } else {
            if (!is_int($offset)) {
                throw new InvalidArgumentException(
                    'Invalid offset for layer, it must be an integer'
                );
            }

            if (count($this) < $offset || 0 > $offset) {
                throw new OutOfBoundsException(sprintf(
                    'Invalid offset for layer, it must be a value between 0 and %d, %d given',
                    count($this), $offset
                ));
            }

            if (isset($this[$offset])) {
                unset($this[$offset]);
                $offset = $offset - 1;
            }
        }

        $frame = $image->getImagick();

        try {
            if (count($this) > 0) {
                $this->resource->setIteratorIndex($offset);
            }
            $this->resource->addImage($frame);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Unable to set the layer', $e->getCode(), $e);
        }

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

        try {
            $this->resource->setIteratorIndex($offset);
            $this->resource->removeImage();
        } catch (\ImagickException $e) {
            throw new RuntimeException('Unable to remove layer', $e->getCode(), $e);
        }
    }
}
