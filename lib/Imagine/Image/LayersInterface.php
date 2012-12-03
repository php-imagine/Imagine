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
 * The layers interface
 */
interface LayersInterface extends \Iterator, \Countable
{
    /**
     * Merge layers into the original objects
     *
     * @throws RuntimeException
     */
    public function merge();
}
