<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Imagick;

use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\ImagineInterface;

/**
 * Imagine implementation using the Imagick PHP extension
 */
final class Imagine implements ImagineInterface
{
    /**
     * @throws RuntimeException
     */
    public function __construct()
    {
        if (!class_exists('Imagick')) {
            throw new RuntimeException('Imagick not installed');
        }

        $imagick = new \Imagick();
        $v = $imagick->getVersion();
        list($version, $year, $month, $day, $q, $website) = sscanf($v['versionString'], 'ImageMagick %s %04d-%02d-%02d %s %s');

        // imagick.setiteratorindex.php
        if (version_compare('6.2.9', $version) > 0) {
            throw new RuntimeException('Imagick version 6.2.9 or higher is required');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function open($path)
    {
        $handle = @fopen($path, 'r');

        if (false === $handle) {
            throw new InvalidArgumentException(sprintf(
                'File %s doesn\'t exist', $path
            ));
        }

        try {
            $image = $this->read($handle);
        } catch (\Exception $e) {
            fclose($handle);
            throw new RuntimeException(sprintf('Unable to open image %s', $path), $e->getCode(), $e);
        }

        return $image;
    }

    /**
     * {@inheritdoc}
     */
    public function create(BoxInterface $size, Color $color = null)
    {
        $width  = $size->getWidth();
        $height = $size->getHeight();

        $color = null !== $color ? $color : new Color('fff');

        try {
            $pixel = new \ImagickPixel((string) $color);
            $pixel->setColorValue(
                \Imagick::COLOR_OPACITY,
                number_format(abs(round($color->getAlpha() / 100, 1)), 1)
            );

            $imagick = new \Imagick();
            $imagick->newImage($width, $height, $pixel);
            $imagick->setImageMatte(true);
            $imagick->setImageBackgroundColor($pixel);

            $pixel->clear();
            $pixel->destroy();

            return new Image($imagick);
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Could not create empty image', $e->getCode(), $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($string)
    {
        try {
            $imagick = new \Imagick();

            $imagick->readImageBlob($string);
            $imagick->setImageMatte(true);

            return new Image($imagick);
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Could not load image from string', $e->getCode(), $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Variable does not contain a stream resource');
        }

        try {
            $imagick = new \Imagick();
            $imagick->readImageFile($resource);
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Could not read image from resource', $e->getCode(), $e
            );
        }

        return new Image($imagick);
    }

    /**
     * {@inheritdoc}
     */
    public function font($file, $size, Color $color)
    {
        return new Font(new \Imagick(), $file, $size, $color);
    }
}
