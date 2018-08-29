<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

use Imagine\Exception\NotSupportedException;
use Imagine\Exception\RuntimeException;
use Imagine\Factory\ClassFactoryInterface;
use Imagine\Image\AbstractLayers;
use Imagine\Image\Metadata\MetadataBag;
use Imagine\Image\Palette\PaletteInterface;

class Layers extends AbstractLayers
{
    /**
     * @var \Imagine\Gd\Image
     */
    private $image;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var resource
     */
    private $resource;

    /**
     * @var \Imagine\Image\Palette\PaletteInterface
     */
    private $palette;

    /**
     * @param \Imagine\Gd\Image $image
     * @param \Imagine\Image\Palette\PaletteInterface $palette
     * @param resource $resource
     * @param int $initialOffset
     *
     * @throws \Imagine\Exception\RuntimeException
     */
    public function __construct(Image $image, PaletteInterface $palette, $resource, $initialOffset = 0)
    {
        if (!is_resource($resource)) {
            throw new RuntimeException('Invalid Gd resource provided');
        }

        $this->image = $image;
        $this->resource = $resource;
        $this->offset = (int) $initialOffset;
        $this->palette = $palette;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\LayersInterface::merge()
     */
    public function merge()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\LayersInterface::coalesce()
     */
    public function coalesce()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\LayersInterface::animate()
     */
    public function animate($format, $delay, $loops)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::current()
     */
    public function current()
    {
        return $this->getClassFactory()->createImage(ClassFactoryInterface::HANDLE_GD, $this->resource, $this->palette, new MetadataBag());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::key()
     */
    public function key()
    {
        return $this->offset;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::next()
     */
    public function next()
    {
        ++$this->offset;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::rewind()
     */
    public function rewind()
    {
        $this->offset = 0;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::valid()
     */
    public function valid()
    {
        return $this->offset < 1;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Countable::count()
     */
    public function count()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return 0 === $offset;
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        if (0 === $offset) {
            return $this->getClassFactory()->createImage(ClassFactoryInterface::HANDLE_GD, $this->resource, $this->palette, new MetadataBag());
        }

        throw new RuntimeException('GD only supports one layer at offset 0');
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        throw new NotSupportedException('GD does not support layer set');
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        throw new NotSupportedException('GD does not support layer unset');
    }
}
