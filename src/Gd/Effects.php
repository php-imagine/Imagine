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

use Imagine\Effects\EffectsInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB as RGBColor;
use Imagine\Utils\Matrix;

/**
 * Effects implementation using the GD PHP extension.
 */
class Effects implements EffectsInterface
{
    /**
     * @var resource|\GdImage
     */
    private $resource;

    /**
     * Initialize the instance.
     *
     * @param resource|\GdImage $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Effects\EffectsInterface::gamma()
     */
    public function gamma($correction)
    {
        if (false === imagegammacorrect($this->resource, 1.0, $correction)) {
            throw new RuntimeException('Failed to apply gamma correction to the image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Effects\EffectsInterface::negative()
     */
    public function negative()
    {
        if (false === imagefilter($this->resource, IMG_FILTER_NEGATE)) {
            throw new RuntimeException('Failed to negate the image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Effects\EffectsInterface::grayscale()
     */
    public function grayscale()
    {
        if (false === imagefilter($this->resource, IMG_FILTER_GRAYSCALE)) {
            throw new RuntimeException('Failed to grayscale the image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Effects\EffectsInterface::colorize()
     */
    public function colorize(ColorInterface $color)
    {
        if (!$color instanceof RGBColor) {
            throw new RuntimeException('Colorize effects only accepts RGB color in GD context');
        }

        if (false === imagefilter($this->resource, IMG_FILTER_COLORIZE, $color->getRed(), $color->getGreen(), $color->getBlue())) {
            throw new RuntimeException('Failed to colorize the image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Effects\EffectsInterface::sharpen()
     */
    public function sharpen()
    {
        $sharpenMatrix = array(array(-1, -1, -1), array(-1, 16, -1), array(-1, -1, -1));
        $divisor = array_sum(array_map('array_sum', $sharpenMatrix));

        if (false === imageconvolution($this->resource, $sharpenMatrix, $divisor, 0)) {
            throw new RuntimeException('Failed to sharpen the image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Effects\EffectsInterface::blur()
     */
    public function blur($sigma = 1)
    {
        if (false === imagefilter($this->resource, IMG_FILTER_GAUSSIAN_BLUR)) {
            throw new RuntimeException('Failed to blur the image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Effects\EffectsInterface::brightness()
     */
    public function brightness($brightness)
    {
        $gdBrightness = (int) round($brightness / 100 * 255);
        if ($gdBrightness < -255 || $gdBrightness > 255) {
            throw new InvalidArgumentException(sprintf('The %1$s argument can range from %2$d to %3$d, but you specified %4$d.', '$brightness', -100, 100, $brightness));
        }
        if (false === imagefilter($this->resource, IMG_FILTER_BRIGHTNESS, $gdBrightness)) {
            throw new RuntimeException('Failed to brightness the image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Effects\EffectsInterface::convolve()
     */
    public function convolve(Matrix $matrix)
    {
        if ($matrix->getWidth() !== 3 || $matrix->getHeight() !== 3) {
            throw new InvalidArgumentException(sprintf('A convolution matrix must be 3x3 (%dx%d provided).', $matrix->getWidth(), $matrix->getHeight()));
        }
        if (false === imageconvolution($this->resource, $matrix->getMatrix(), 1, 0)) {
            throw new RuntimeException('Failed to convolve the image');
        }

        return $this;
    }
}
