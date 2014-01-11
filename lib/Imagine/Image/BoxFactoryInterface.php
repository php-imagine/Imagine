<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

/**
 * Interface for box factory
 */
interface BoxFactoryInterface
{
    /**
     * Creates box with given size
     * 
     * @param integer $width
     * @param integer $height
     * 
     * @return BoxInterface
     * @throws InvalidArgumentException
     */
    public function create($width, $height);
}