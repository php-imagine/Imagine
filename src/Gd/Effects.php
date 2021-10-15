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

use Imagine\Driver\InfoProvider;
use Imagine\Effects\EffectsInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB as RGBColor;
use Imagine\Utils\Matrix;

/**
 * Effects implementation using the GD PHP extension.
 */
class Effects implements EffectsInterface, InfoProvider
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
     * @see \Imagine\Driver\InfoProvider::getDriverInfo()
     * @since 1.3.0
     */
    public static function getDriverInfo($required = true)
    {
        return DriverInfo::get($required);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Effects\EffectsInterface::gamma()
     */
    public function gamma($correction)
    {
        if (imagegammacorrect($this->resource, 1.0, $correction) === false) {
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
        if (imagefilter($this->resource, IMG_FILTER_NEGATE) === false) {
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
        if (imagefilter($this->resource, IMG_FILTER_GRAYSCALE) === false) {
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

        if (imagefilter($this->resource, IMG_FILTER_COLORIZE, $color->getRed(), $color->getGreen(), $color->getBlue()) === false) {
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

        if (imageconvolution($this->resource, $sharpenMatrix, $divisor, 0) === false) {
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
        if (imagefilter($this->resource, IMG_FILTER_GAUSSIAN_BLUR) === false) {
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
        if (imagefilter($this->resource, IMG_FILTER_BRIGHTNESS, $gdBrightness) === false) {
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
        if (imageconvolution($this->resource, $matrix->getMatrix(), 1, 0) === false) {
            throw new RuntimeException('Failed to convolve the image');
        }

        return $this;
    }
}
