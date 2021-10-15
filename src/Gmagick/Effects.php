<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gmagick;

use Imagine\Driver\InfoProvider;
use Imagine\Effects\EffectsInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Utils\Matrix;

/**
 * Effects implementation using the Gmagick PHP extension.
 */
class Effects implements EffectsInterface, InfoProvider
{
    /**
     * @var \Gmagick
     */
    private $gmagick;

    /**
     * Initialize the instance.
     *
     * @param \Gmagick $gmagick
     */
    public function __construct(\Gmagick $gmagick)
    {
        $this->gmagick = $gmagick;
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
        try {
            $this->gmagick->gammaimage($correction);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Failed to apply gamma correction to the image', $e->getCode(), $e);
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
        static::getDriverInfo()->requireFeature(DriverInfo::FEATURE_NEGATEIMAGE);
        try {
            $this->gmagick->negateimage(false, \Gmagick::CHANNEL_ALL);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Failed to negate the image', $e->getCode(), $e);
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
        try {
            $this->gmagick->setimagetype(defined('Gmagick::IMGTYPE_GRAYSCALE') ? \Gmagick::IMGTYPE_GRAYSCALE : 2);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Failed to grayscale the image', $e->getCode(), $e);
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
        static::getDriverInfo()->requireFeature(DriverInfo::FEATURE_COLORIZEIMAGE);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Effects\EffectsInterface::sharpen()
     */
    public function sharpen()
    {
        static::getDriverInfo()->requireFeature(DriverInfo::FEATURE_SHARPENIMAGE);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Effects\EffectsInterface::blur()
     */
    public function blur($sigma = 1)
    {
        try {
            $this->gmagick->blurimage(0, $sigma);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Failed to blur the image', $e->getCode(), $e);
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
        $brightness = (int) round($brightness);
        if ($brightness < -100 || $brightness > 100) {
            throw new InvalidArgumentException(sprintf('The %1$s argument can range from %2$d to %3$d, but you specified %4$d.', '$brightness', -100, 100, $brightness));
        }
        try {
            // This *emulates* setting the brightness
            $sign = $brightness < 0 ? -1 : 1;
            $v = abs($brightness) / 100;
            if ($sign > 0) {
                $v = (2 / (sin(($v * .99999 * M_PI_2) + M_PI_2))) - 2;
            }
            $this->gmagick->modulateimage(100 + $sign * $v * 100, 100, 100);
        } catch (\GmagickException $e) {
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
        static::getDriverInfo()->requireFeature(DriverInfo::FEATURE_CONVOLVEIMAGE);
        if ($matrix->getWidth() !== 3 || $matrix->getHeight() !== 3) {
            throw new InvalidArgumentException(sprintf('A convolution matrix must be 3x3 (%dx%d provided).', $matrix->getWidth(), $matrix->getHeight()));
        }
        try {
            $this->gmagick->convolveimage($matrix->getValueList());
        } catch (\GmagickException $e) {
            throw new RuntimeException('Failed to convolve the image');
        }

        return $this;
    }
}
