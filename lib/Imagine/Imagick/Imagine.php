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

final class Imagine implements ImagineInterface
{
    /**
     * @throws Imagine\Exception\RuntimeException
     */
    public function __construct()
    {
        if (!class_exists('Imagick')) {
            throw new RuntimeException('Imagick not installed');
        }

        $imagick = new \Imagick();
        $v = $imagick->getVersion();
        list($version, $year, $month, $day, $q, $website) = sscanf($v['versionString'], 'ImageMagick %s %04d-%02d-%02d %s %s');

        if (version_compare('6.2.9', $version) > 0) {
            throw new RuntimeException('Imagick version 6.2.9 or higher is required');
        }
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImagineInterface::open()
     */
    public function open($path)
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf(
                'File %s doesn\'t exist', $path
            ));
        }

        try {
            $imagick = new \Imagick($path);

            $imagick->setImageMatte(true);

            return new Image($imagick);
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                sprintf('Could not open path "%s"', $path), $e->getCode(), $e
            );
        }
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImagineInterface::create()
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
     * (non-PHPdoc)
     * @see Imagine\Image\ImagineInterface::load()
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
     * (non-PHPdoc)
     * @see Imagine\Image\ImagineInterface::font()
     */
    public function font($file, $size, Color $color)
    {
        return new Font(new \Imagick(), $file, $size, $color);
    }
}
