<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter;

use Imagine\Image\ImageInterface;

/**
 * Interface for imagine filters.
 */
interface FilterInterface
{
    /**
     * Applies scheduled transformation to an ImageInterface instance.
     *
     * @param \Imagine\Image\ImageInterface $image
     *
     * @return \Imagine\Image\ImageInterface returns the processed ImageInterface instance
     */
    public function apply(ImageInterface $image);
}
