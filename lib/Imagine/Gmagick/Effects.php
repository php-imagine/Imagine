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

use Imagine\Effects\EffectsInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\NotSupportedException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\Effects\ConvolutionMatrixInterface;
use Imagine\Image\Palette\Color\ColorInterface;

/**
 * Effects implementation using the Gmagick PHP extension.
 */
class Effects implements EffectsInterface
{
    private $gmagick;

    public function __construct(\Gmagick $gmagick)
    {
        $this->gmagick = $gmagick;
    }

    /**
     * {@inheritdoc}
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
     */
    public function negative()
    {
        if (!method_exists($this->gmagick, 'negateimage')) {
            throw new NotSupportedException('Gmagick version 1.1.0 RC3 is required for negative effect');
        }

        try {
            $this->gmagick->negateimage(false, \Gmagick::CHANNEL_ALL);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Failed to negate the image', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function grayscale()
    {
        try {
            $this->gmagick->setImageType(2);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Failed to grayscale the image', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function colorize(ColorInterface $color)
    {
        throw new NotSupportedException('Gmagick does not support colorize');
    }

    /**
     * {@inheritdoc}
     */
    public function sharpen()
    {
        throw new NotSupportedException('Gmagick does not support sharpen yet');
    }

    /**
     * {@inheritdoc}
     */
    public function blur($sigma = 1)
    {
        try {
            $this->gmagick->blurImage(0, $sigma);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Failed to blur the image', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
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
     */
    public function convolve(ConvolutionMatrixInterface $matrix)
    {
        throw new NotSupportedException('Gmagick does not support convolve yet');
    }
}
