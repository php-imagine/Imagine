<?php

namespace Imagine\Filter;

use Imagine\ImageInterface;

interface FilterInterface
{
    /**
     * Applies scheduled transformation to ImageInterface instance
     * Returns processed ImageInterface instance
     *
     * @param ImageInterface $image
     *
     * @return ImageInterface
     */
    function apply(ImageInterface $image);
}
