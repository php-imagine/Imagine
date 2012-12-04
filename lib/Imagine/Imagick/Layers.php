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
    public function current()
    {
        if (!isset($this->layers[$this->offset])) {
            try {
                $this->resource->setIteratorIndex($this->offset);
                $this->layers[$this->offset] = $this->resource->getImage();
            } catch (\GmagickException $e) {
                throw new RuntimeException(
                    sprintf('Failed to extract layer %d', $this->offset),
                    $e->getCode(), $e
                );
            }
        }

        return new Image($this->layers[$this->offset]);
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
}
