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

interface ColorInterface
{
    /**
     * Channel name: red.
     *
     * @var string
     */
    const COLOR_RED = 'red';

    /**
     * Channel name: green.
     *
     * @var string
     */
    const COLOR_GREEN = 'green';

    /**
     * Channel name: blue.
     *
     * @var string
     */
    const COLOR_BLUE = 'blue';

    /**
     * Channel name: cyan.
     *
     * @var string
     */
    const COLOR_CYAN = 'cyan';

    /**
     * Channel name: magenta.
     *
     * @var string
     */
    const COLOR_MAGENTA = 'magenta';

    /**
     * Channel name: yellow.
     *
     * @var string
     */
    const COLOR_YELLOW = 'yellow';

    /**
     * Channel name: key (black).
     *
     * @var string
     */
    const COLOR_KEYLINE = 'keyline';

    /**
     * Channel name: gray.
     *
     * @var string
     */
    const COLOR_GRAY = 'gray';

    /**
     * Return the value of one of the component.
     *
     * @param string $component One of the ColorInterface::COLOR_* component
     *
     * @throws \Imagine\Exception\InvalidArgumentException if $component is not valid
     *
     * @return int|null
     */
    public function getValue($component);

    /**
     * Returns percentage of transparency of the color (from 0 - fully transparent, to 100 - fully opaque).
     *
     * @return int|null return NULL if the color type does not support transparency
     */
    public function getAlpha();

    /**
     * Returns the palette attached to the current color.
     *
     * @return \Imagine\Image\Palette\PaletteInterface
     */
    public function getPalette();

    /**
     * Returns a copy of current color, incrementing the alpha channel by the given amount.
     *
     * @param int $alpha
     *
     * @throws \Imagine\Exception\RuntimeException if the color type does not support transparency
     *
     * @return static
     */
    public function dissolve($alpha);

    /**
     * Returns a copy of the current color, lightened by the specified number of shades.
     *
     * @param int $shade
     *
     * @return static
     */
    public function lighten($shade);

    /**
     * Returns a copy of the current color, darkened by the specified number of shades.
     *
     * @param int $shade
     *
     * @return static
     */
    public function darken($shade);

    /**
     * Returns a gray related to the current color.
     *
     * @return static
     */
    public function grayscale();

    /**
     * Checks if the current color is opaque.
     *
     * @return bool
     */
    public function isOpaque();

    /**
     * Returns hex representation of the color.
     *
     * @return string
     */
    public function __toString();
}
