<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image\Palette;

use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\ProfileInterface;

/**
 * Interface that any palette must implement.
 */
interface PaletteInterface
{
    /**
     * Palette name: grayscale.
     *
     * @var string
     */
    const PALETTE_GRAYSCALE = 'gray';

    /**
     * Palette name: RGB.
     *
     * @var string
     */
    const PALETTE_RGB = 'rgb';

    /**
     * Palette name: CMYK.
     *
     * @var string
     */
    const PALETTE_CMYK = 'cmyk';

    /**
     * Returns a color given some values.
     *
     * @param string|int[]|int $color A color
     * @param int|null $alpha Set alpha to null to disable it
     *
     * @throws \Imagine\Exception\InvalidArgumentException In case you pass an alpha value to a Palette that does not support alpha
     *
     * @return \Imagine\Image\Palette\Color\ColorInterface
     */
    public function color($color, $alpha = null);

    /**
     * Blend two colors given an amount.
     *
     * @param \Imagine\Image\Palette\Color\ColorInterface $color1
     * @param \Imagine\Image\Palette\Color\ColorInterface $color2
     * @param float $amount The amount of color2 in color1
     *
     * @return \Imagine\Image\Palette\Color\ColorInterface
     */
    public function blend(ColorInterface $color1, ColorInterface $color2, $amount);

    /**
     * Attachs an ICC profile to this Palette.
     *
     * (A default profile is provided by default)
     *
     * @param \Imagine\Image\ProfileInterface $profile
     *
     * @return $this
     */
    public function useProfile(ProfileInterface $profile);

    /**
     * Returns the ICC profile attached to this Palette.
     *
     * @return \Imagine\Image\ProfileInterface
     */
    public function profile();

    /**
     * Returns the name of this Palette, one of PaletteInterface::PALETTE_ constants.
     *
     * @return string
     */
    public function name();

    /**
     * Returns an array containing ColorInterface::COLOR_* constants that
     * define the structure of colors for a pixel.
     *
     * @return string[]
     */
    public function pixelDefinition();

    /**
     * Tells if alpha channel is supported in this palette.
     *
     * @return bool
     */
    public function supportsAlpha();

    /**
     * Get the max value of palette components (255 for RGB and Grayscale, 100 for CMYK).
     *
     * @return int
     */
    public function getChannelsMaxValue();
}
