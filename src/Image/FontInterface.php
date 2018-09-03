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
 * The font interface.
 */
interface FontInterface
{
    /**
     * Gets the fontfile for current font.
     *
     * @return string
     */
    public function getFile();

    /**
     * Gets font's integer point size.
     *
     * @return int
     */
    public function getSize();

    /**
     * Gets font's color.
     *
     * @return \Imagine\Image\Palette\Color\ColorInterface
     */
    public function getColor();

    /**
     * Gets BoxInterface of font size on the image based on string and angle.
     *
     * @param string $string
     * @param int $angle
     *
     * @return \Imagine\Image\BoxInterface
     */
    public function box($string, $angle = 0);

    /**
     * Split a string into multiple lines so that it fits a specific width.
     *
     * @param string $string The text to be wrapped
     * @param int $maxWidth The maximum width of the text
     * @param int $angle
     *
     * @return string
     */
    public function wrapText($string, $maxWidth, $angle = 0);
}
