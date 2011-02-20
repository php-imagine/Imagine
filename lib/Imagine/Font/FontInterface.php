<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Font;

interface FontInterface
{
    /**
     * @param string $text
     *
     * @return Imagine\SizeInterface
     */
    function getSize($text);

    /**
     * @param string $text
     *
     * @return Imagine\ImageInterface
     */
    function mask($text);
}
