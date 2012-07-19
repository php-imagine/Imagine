<?php

namespace Imagine\Filter\Advanced;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

/**
 * The OnPixelBased takes a callable, and for each pixel, this callable is called with the
 * image  (\Imagine\Image\ImageInterface) and the current point (\Imagine\Image\Point)
 */
class OnPixelBased implements FilterInterface
{
    protected $callback;

    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('$callback has to be callable');
        }

        $this->callback = $callback;
    }

    /**
     * Applies scheduled transformation to ImageInterface instance
     * Returns processed ImageInterface instance
     *
     * @param ImageInterface $image
     *
     * @return ImageInterface
     */
    public function apply(ImageInterface $image)
    {
        for ($x = 0; $x < $image->getSize()->getWidth(); $x++) {
            for ($y = 0; $y < $image->getSize()->getHeight(); $y++) {
                call_user_func($this->callback, $image, new Point($x, $y));
            }
        }

        return $image;
    }
}
