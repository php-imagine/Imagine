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
use Imagine\BoxInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\ImagineInterface;

class Imagine implements ImagineInterface
{
    public function __construct()
    {
        if (!class_exists('Gmagick')) {
            throw new RuntimeException('Gmagick not installed');
        }
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImagineInterface::open()
     */
    public function open($path)
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf(
                'File %s doesn\'t exist', $path
            ));
        }

        return new Image(new \Gmagick($path), $this);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImagineInterface::create()
     */
    public function create(BoxInterface $size, Color $color = null)
    {
        $width   = $size->getWidth();
        $height  = $size->getHeight();
        $color   = null !== $color ? $color : new Color('fff');
        $gmagick = new \Gmagick();
        $pixel   = new \GmagickPixel((string) $color);

        if ($color->getAlpha() > 0) {
            // TODO: implement support for transparent background
            throw new RuntimeException('alpha transparency not implemented');
        }

        $gmagick->newimage($width, $height, $pixel->getcolor(false));
        $gmagick->setimagecolorspace(\Gmagick::COLORSPACE_TRANSPARENT);
        // this is needed to propagate transparency
        $gmagick->setimagebackgroundcolor($pixel);

        return new Image($gmagick, $this);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImagineInterface::load()
     */
    public function load($string)
    {
        $gmagick = new \Gmagick();
        $gmagick->readimageblob($string);
        return new Image($gmagick, $this);
    }
}
