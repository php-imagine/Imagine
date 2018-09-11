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
use Imagine\Image\Palette\Grayscale;

final class Gray implements ColorInterface
{
    /**
     * @var int
     */
    private $gray;

    /**
     * @var int
     */
    private $alpha;

    /**
     * @var \Imagine\Image\Palette\Grayscale
     */
    private $palette;

    /**
     * @param \Imagine\Image\Palette\Grayscale $palette
     * @param int[] $color
     * @param int $alpha
     */
    public function __construct(Grayscale $palette, array $color, $alpha)
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
            case ColorInterface::COLOR_GRAY:
                return $this->getGray();
            default:
                throw new InvalidArgumentException(sprintf('Color component %s is not valid', $component));
        }
    }

    /**
     * Returns Gray value of the color (from 0 to 255).
     *
     * @return int
     */
    public function getGray()
    {
        return $this->gray;
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
            array($this->gray),
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
        return $this->palette->color(array(min(255, $this->gray + $shade)), $this->alpha);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::darken()
     */
    public function darken($shade)
    {
        return $this->palette->color(array(max(0, $this->gray - $shade)), $this->alpha);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::grayscale()
     */
    public function grayscale()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::isOpaque()
     */
    public function isOpaque()
    {
        return 100 === $this->alpha;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Palette\Color\ColorInterface::__toString()
     */
    public function __toString()
    {
        return sprintf('#%02x%02x%02x', $this->gray, $this->gray, $this->gray);
    }

    /**
     * Performs checks for validity of given alpha value and sets it.
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
     * Performs checks for color validity (array of array(gray)).
     *
     * @param int[] $color
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    private function setColor(array $color)
    {
        if (count($color) !== 1) {
            throw new InvalidArgumentException('Color argument must look like array(gray), where gray is the integer value between 0 and 255 for the grayscale');
        }

        $color = array_values($color);
        $color[0] = max(0, min(255, $color[0]));

        list($this->gray) = $color;
    }
}
