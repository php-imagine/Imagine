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

use Imagine\Driver\InfoProvider;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;
use Imagine\Factory\ClassFactoryInterface;
use Imagine\Image\AbstractLayers;
use Imagine\Image\Format;
use Imagine\Image\Metadata\MetadataBag;
use Imagine\Image\Palette\PaletteInterface;

class Layers extends AbstractLayers implements InfoProvider
{
    /**
     * @var \Imagine\Imagick\Image
     */
    private $image;

    /**
     * @var \Imagick
     */
    private $resource;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var \Imagine\Imagick\Image[]
     */
    private $layers = array();

    /**
     * @var \Imagine\Image\Palette\PaletteInterface
     */
    private $palette;

    public function __construct(Image $image, PaletteInterface $palette, \Imagick $resource, $initialOffset = 0)
    {
        $this->image = $image;
        $this->resource = $resource;
        $this->palette = $palette;
        $this->offset = (int) $initialOffset;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\InfoProvider::getDriverInfo()
     * @since 1.3.0
     */
    public static function getDriverInfo($required = true)
    {
        return DriverInfo::get($required);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\LayersInterface::merge()
     */
    public function merge()
    {
        foreach ($this->layers as $offset => $image) {
            try {
                $this->resource->setIteratorIndex($offset);
                $this->resource->setImage($image->getImagick());
            } catch (\ImagickException $e) {
                throw new RuntimeException('Failed to substitute layer', $e->getCode(), $e);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\LayersInterface::animate()
     */
    public function animate($format, $delay, $loops)
    {
        $formatInfo = Format::get($format);
        if ($formatInfo === null || $formatInfo->getID() !== Format::ID_GIF) {
            throw new InvalidArgumentException('Animated picture is currently only supported on gif');
        }

        if (!is_int($loops) || $loops < 0) {
            throw new InvalidArgumentException('Loops must be a positive integer.');
        }

        if ($delay !== null && (!is_int($delay) || $delay < 0)) {
            throw new InvalidArgumentException('Delay must be either null or a positive integer.');
        }

        try {
            foreach ($this as $offset => $layer) {
                $this->resource->setIteratorIndex($offset);
                $this->resource->setFormat(Format::ID_GIF);

                if ($delay !== null) {
                    $layer->getImagick()->setImageDelay($delay / 10);
                    $layer->getImagick()->setImageTicksPerSecond(100);
                }
                $layer->getImagick()->setImageIterations($loops);

                $this->resource->setImage($layer->getImagick());
            }
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to animate layers', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\LayersInterface::coalesce()
     */
    public function coalesce()
    {
        try {
            $coalescedResource = $this->resource->coalesceImages();
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to coalesce layers', $e->getCode(), $e);
        }

        $count = $coalescedResource->getNumberImages();
        for ($offset = 0; $offset < $count; $offset++) {
            try {
                $coalescedResource->setIteratorIndex($offset);
                $this->layers[$offset] = $this->getClassFactory()->createImage(ClassFactoryInterface::HANDLE_IMAGICK, $coalescedResource->getImage(), $this->palette, new MetadataBag());
            } catch (\ImagickException $e) {
                throw new RuntimeException('Failed to retrieve layer', $e->getCode(), $e);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::current()
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->extractAt($this->offset);
    }

    /**
     * Tries to extract layer at given offset.
     *
     * @param int $offset
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return \Imagine\Imagick\Image
     */
    private function extractAt($offset)
    {
        if (!isset($this->layers[$offset])) {
            try {
                $this->resource->setIteratorIndex($offset);
                $this->layers[$offset] = $this->getClassFactory()->createImage(ClassFactoryInterface::HANDLE_IMAGICK, $this->resource->getImage(), $this->palette, new MetadataBag());
            } catch (\ImagickException $e) {
                throw new RuntimeException(sprintf('Failed to extract layer %d', $offset), $e->getCode(), $e);
            }
        }

        return $this->layers[$offset];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::key()
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->offset;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::next()
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        ++$this->offset;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::rewind()
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->offset = 0;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::valid()
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->offset < count($this);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Countable::count()
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        try {
            return $this->resource->getNumberImages();
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to count the number of layers', $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetExists()
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return is_int($offset) && $offset >= 0 && $offset < count($this);
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetGet()
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->extractAt($offset);
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetSet()
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $image)
    {
        if (!$image instanceof Image) {
            throw new InvalidArgumentException('Only an Imagick Image can be used as layer');
        }

        if ($offset === null) {
            $offset = count($this) - 1;
        } else {
            if (!is_int($offset)) {
                throw new InvalidArgumentException('Invalid offset for layer, it must be an integer');
            }

            if (count($this) < $offset || $offset < 0) {
                throw new OutOfBoundsException(sprintf('Invalid offset for layer, it must be a value between 0 and %d, %d given', count($this), $offset));
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
     *
     * @see \ArrayAccess::offsetUnset()
     */
    #[\ReturnTypeWillChange]
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
