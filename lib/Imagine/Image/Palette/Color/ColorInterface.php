<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image\Palette\Color;

use Imagine\Image\Palette\PaletteInterface;

interface ColorInterface
{
    const COLOR_RED = 'red';
    const COLOR_GREEN = 'green';
    const COLOR_BLUE = 'blue';

    const COLOR_CYAN = 'cyan';
    const COLOR_MAGENTA = 'magenta';
    const COLOR_YELLOW = 'yellow';
    const COLOR_KEYLINE = 'keyline';

    const COLOR_GRAY = 'gray';

    /**
     * Return the value of one of the component.
     *
     * @param string $value One of the ColorInterface::COLOR_* component
     *
     * @return Integer
     */
    public function getValue($component);

    /**
     * Returns percentage of transparency of the color
     *
     * @return integer
     */
    public function getAlpha();

    /**
     * Returns the palette attached to the current color
     *
     * @return PaletteInterface
     */
    public function getPalette();

    /**
     * Returns a copy of current color, incrementing the alpha channel by the
     * given amount
     *
     * @param integer $alpha
     *
     * @return ColorInterface
     */
    public function dissolve($alpha);

    /**
     * Returns a copy of the current color, lightened by the specified number
     * of shades
     *
     * @param integer $shade
     *
     * @return ColorInterface
     */
    public function lighten($shade);

    /**
     * Returns a copy of the current color, darkened by the specified number of
     * shades
     *
     * @param integer $shade
     *
     * @return ColorInterface
     */
    public function darken($shade);

    /**
     * Returns a gray related to the current color
     *
     * @return ColorInterface
     */
    public function grayscale();

    /**
     * Checks if the current color is opaque
     *
     * @return Boolean
     */
    public function isOpaque();
}
