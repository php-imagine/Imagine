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

interface GdInterface
{
    /**
     * @param integer $width
     * @param integer $height
     *
     * @return Imagine\Gd\ResourceInterface|null
     */
    function create($width, $height);

    /**
     * @param string $path
     *
     * @return Imagine\Gd\ResourceInterface|null
     */
    function open($path);

    /**
     * @param string $string
     *
     * @return Imagine\Gd\ResourceInterface|null
     */
    function load($string);
}
