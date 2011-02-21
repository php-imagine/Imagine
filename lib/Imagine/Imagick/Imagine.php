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

use Imagine\Color;
use Imagine\BoxInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\ImageInterface;
use Imagine\ImagineInterface;

final class Imagine implements ImagineInterface
{
    public function __construct()
    {
        if (!class_exists('Imagick')) {
            throw new RuntimeException('Imagick not installed');
        }
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImagineInterface::open()
     */
    public function open($path)
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf(
                'File %s doesn\'t exist', $path
            ));
        }

        try {
            return new Image(new \Imagick($path));
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                sprintf('Could not open path "%s"', $path), $e->getCode(), $e
            );
        }
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImagineInterface::create()
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

            $pixel->clear();
            $pixel->destroy();

            return new Image($imagick);
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Could not create empty image', $e->getCode(), $e
            );
        }
    }
}
