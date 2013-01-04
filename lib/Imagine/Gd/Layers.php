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

use Imagine\Image\LayersInterface;
use Imagine\Exception\RuntimeException;

class Layers implements LayersInterface
{
    private $image;
    private $offset;
    private $gd;

    public function __construct(Image $image, Gd $gd)
    {
        if (!is_resource($gd->resource) || get_resource_type($gd->resource) !== "gd") {
            throw new RuntimeException('Invalid Gd resource provided');
        }

        $this->image = $image;
        $this->gd = $gd;
        $this->offset = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function merge()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function coalesce()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function replace($offset, ImageInterface $image)
    {
        if ($offset !== 0) {
            throw new RuntimeException("Index out of bounds: $offset");
        }

        if (!$image instanceof Image) {
            throw new RuntimeException("Replacement image must be Gd image.");
        }

        $this->gd = $image->getResource();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return new Image($this->gd);
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
        return $this->offset < 1;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return 1;
    }
}
