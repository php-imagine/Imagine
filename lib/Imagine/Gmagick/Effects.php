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
use Imagine\Exception\RuntimeException;
use Imagine\Image\Color;

/**
 * Effects implementation using the Gmagick PHP extension
 */
class Effects implements EffectsInterface
{
    protected $gmagick;

    public function __construct(\Gmagick $gmagick)
    {
        $this->gmagick = $gmagick;
    }

    /**
     * {@inheritdoc}
     */
    public static function create($resource)
    {
        return new static($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function gamma($correction)
    {
        try {
            $this->gmagick->gammaimage($correction);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Failed to apply gamma correction to the image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function negative()
    {
        if (!method_exists($this->gmagick, 'negateimage')) {
            throw new RuntimeException('Gmagick version 1.1.0 RC3 is required'
                . ' for negative effect');
        }

        try {
            $this->gmagick->negateimage(false, \Gmagick::CHANNEL_ALL);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Failed to negate the image');
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
            throw new RuntimeException('Failed to grayscale the image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function colorize(Color $color)
    {
        throw new RuntimeException('Gmagick does not support colorize');
    }

    /**
     * {@inheritdoc}
     */
    public function sharpen()
    {
        throw new RuntimeException('Gmagick does not support sharpen yet');
    }
}
