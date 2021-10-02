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

use Imagine\Exception\InvalidArgumentException;
use Imagine\Image\Palette\RGB as RGBPalette;

final class RGB implements ColorInterface
{
    /**
     * @var int
     */
    private $r;

    /**
     * @var int
     */
    private $g;

    /**
     * @var int
     */
    private $b;

    /**
     * @var int
     */
    private $alpha;

    /**
     * @var \Imagine\Image\Palette\RGB
     */
    private $palette;

    /**
     * @param \Imagine\Image\Palette\RGB $palette
     * @param int[] $color
     * @param int $alpha
     */
    public function __construct(RGBPalette $palette, array $color, $alpha)
    {
        $this->palette = $palette;
        $this->setColor($color);
        $this->setAlpha($alpha);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::getValue()
     */
    public function getValue($component)
    {
        switch ($component) {
            case ColorInterface::COLOR_RED:
                return $this->getRed();
            case ColorInterface::COLOR_GREEN:
                return $this->getGreen();
            case ColorInterface::COLOR_BLUE:
                return $this->getBlue();
            default:
                throw new InvalidArgumentException(sprintf('Color component %s is not valid', $component));
        }
    }

    /**
     * Returns RED value of the color (from 0 to 255).
     *
     * @return int
     */
    public function getRed()
    {
        return $this->r;
    }

    /**
     * Returns GREEN value of the color (from 0 to 255).
     *
     * @return int
     */
    public function getGreen()
    {
        return $this->g;
    }

    /**
     * Returns BLUE value of the color (from 0 to 255).
     *
     * @return int
     */
    public function getBlue()
    {
        return $this->b;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::getPalette()
     */
    public function getPalette()
    {
        return $this->palette;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::getAlpha()
     */
    public function getAlpha()
    {
        return $this->alpha;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::dissolve()
     */
    public function dissolve($alpha)
    {
        return $this->palette->color(
            array($this->r, $this->g, $this->b),
            min(max((int) round($this->alpha + $alpha), 0), 100)
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::lighten()
     */
    public function lighten($shade)
    {
        return $this->palette->color(
            array(
                min(255, $this->r + $shade),
                min(255, $this->g + $shade),
                min(255, $this->b + $shade),
            ),
            $this->alpha
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::darken()
     */
    public function darken($shade)
    {
        return $this->palette->color(
            array(
                max(0, $this->r - $shade),
                max(0, $this->g - $shade),
                max(0, $this->b - $shade),
            ),
            $this->alpha
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::grayscale()
     */
    public function grayscale()
    {
        $gray = min(255, round(0.299 * $this->getRed() + 0.114 * $this->getBlue() + 0.587 * $this->getGreen()));

        return $this->palette->color(array($gray, $gray, $gray), $this->alpha);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::isOpaque()
     */
    public function isOpaque()
    {
        return $this->alpha === 100;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::__toString()
     */
    public function __toString()
    {
        return sprintf('#%02x%02x%02x', $this->r, $this->g, $this->b);
    }

    /**
     * Internal.
     *
     * Performs checks for validity of given alpha value and sets it
     *
     * @param int $alpha
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    private function setAlpha($alpha)
    {
        if (!is_int($alpha) || $alpha < 0 || $alpha > 100) {
            throw new InvalidArgumentException(sprintf('Alpha must be an integer between 0 and 100, %s given', $alpha));
        }

        $this->alpha = $alpha;
    }

    /**
     * Internal.
     *
     * Performs checks for color validity (array of array(R, G, B))
     *
     * @param array $color
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    private function setColor(array $color)
    {
        if (count($color) !== 3) {
            throw new InvalidArgumentException('Color argument must look like array(R, G, B), where R, G, B are the integer values between 0 and 255 for red, green and blue color indexes accordingly');
        }

        $colors = array_values($color);
        array_walk($colors, function (&$color) {
            $color = max(0, min(255, $color));
        });

        list($this->r, $this->g, $this->b) = $colors;
    }
}
