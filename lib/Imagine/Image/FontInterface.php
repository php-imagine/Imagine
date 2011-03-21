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

interface FontInterface
{
    /**
     * Gets the fontfile for current font
     *
     * @return string
     */
    function getFile();

    /**
     * Gets font's integer point size
     *
     * @return integer
     */
    function getSize();

    /**
     * Gets font's color
     *
     * @return Imagine\Image\Color
     */
    function getColor();

    /**
     * Gets BoxInterface of font size on the image based on string and angle
     *
     * @param string  $string
     * @param integer $angle
     *
     * @return Imagine\Image\BoxInterface
     */
    function box($string, $angle = 0);
}
