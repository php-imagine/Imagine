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

use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB as RGBColor;
use Imagine\Image\Profile;
use Imagine\Image\ProfileInterface;

class RGB implements PaletteInterface
{
    /**
     * @var ColorParser
     */
    private $parser;

    /**
     * @var ProfileInterface
     */
    private $profile;

    /**
     * @var array
     */
    protected static $colors = array();

    public function __construct()
    {
        $this->parser = new ColorParser();
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return PaletteInterface::PALETTE_RGB;
    }

    /**
     * {@inheritdoc}
     */
    public function pixelDefinition()
    {
        return array(
            ColorInterface::COLOR_RED,
            ColorInterface::COLOR_GREEN,
            ColorInterface::COLOR_BLUE,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAlpha()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function useProfile(ProfileInterface $profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function profile()
    {
        if (!$this->profile) {
            $this->profile = Profile::fromPath(__DIR__ . '/../../resources/color.org/sRGB_IEC61966-2-1_black_scaled.icc');
        }

        return $this->profile;
    }

    /**
     * {@inheritdoc}
     */
    public function color($color, $alpha = null)
    {
        if (null === $alpha) {
            $alpha = 100;
        }

        $color = $this->parser->parseToRGB($color);
        $index = sprintf('#%02x%02x%02x-%d', $color[0], $color[1], $color[2], $alpha);

        if (false === array_key_exists($index, static::$colors)) {
            static::$colors[$index] = new RGBColor($this, $color, $alpha);
        }

        return static::$colors[$index];
    }

    /**
     * {@inheritdoc}
     */
    public function blend(ColorInterface $color1, ColorInterface $color2, $amount)
    {
        if (!$color1 instanceof RGBColor || !$color2 instanceof RGBColor) {
            throw new RuntimeException('RGB palette can only blend RGB colors');
        }

        $amout = (float) $amount;
        if ($amout < 0.0 || $amout > 1.0) {
            throw new InvalidArgumentException(sprintf('The %1$s argument can range from %2$d to %3$d, but you specified %4$d.', '$amount', 0, 1, $amount));
        }
        $amountComplement = 1 - $amount;

        return $this->color(
            array(
                (int) $color2->getRed() * $amount + $color1->getRed() * $amountComplement,
                (int) $color2->getGreen() * $amount + $color1->getGreen() * $amountComplement,
                (int) $color2->getBlue() * $amount + $color1->getBlue() * $amountComplement,
            ),
            (int) $color2->getAlpha() * $amount + $color1->getAlpha() * $amountComplement
        );
    }
}
