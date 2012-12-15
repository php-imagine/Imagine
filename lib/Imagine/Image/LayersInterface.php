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

    /**
     * Coalesce layers. Each layer in the sequence is the same size as the first and composited with the next layer in
     * the sequence.
     */
    public function coalesce();
}
