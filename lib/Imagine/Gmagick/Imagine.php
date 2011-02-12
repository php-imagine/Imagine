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

use Imagine\Color;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\ImagineInterface;

class Imagine implements ImagineInterface
{
    public function __construct()
    {
        if (!class_exists('Gmagick')) {
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
            throw new InvalidArgumentException(sprintf('File %s doesn\'t '.
                'exist', $path));
        }

        return new Image(new \Gmagick($path), $this);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImagineInterface::create()
     */
    public function create($width, $height, Color $color = null)
    {
        if ($width < 1 || $height < 1) {
            throw new InvalidArgumentException('Width an height of the '.
                'resize must be positive integers');
        }

        $color = null !== $color ? $color : new Color('fff');

        $gmagick = new \Gmagick();
        $pixel = new \GmagickPixel(sprintf('rgba(%d,%d,%d,%d)',
            $color->getRed(), $color->getGreen(), $color->getBlue(),
            abs(1 - round($color->getAlpha() / 100, 1))
        ));
        $gmagick->newImage($width, $height, $pixel->getcolor(false));

        return new Image($gmagick, $this);
    }
}
