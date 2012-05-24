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

use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\ImagineInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;

class Imagine implements ImagineInterface
{
    /**
     * @throws Imagine\Exception\RuntimeException
     */
    public function __construct()
    {
        if (!class_exists('Gmagick')) {
            throw new RuntimeException('Gmagick not installed');
        }
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImagineInterface::open()
     */
    public function open($path)
    {
        $handle = @fopen($path, 'r');

        if (false === $handle) {
            throw new InvalidArgumentException(sprintf(
                'File %s doesn\'t exist', $path
            ));
        }

        return $this->read($handle);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImagineInterface::create()
     */
    public function create(BoxInterface $size, Color $color = null)
    {
        $width   = $size->getWidth();
        $height  = $size->getHeight();
        $color   = null !== $color ? $color : new Color('fff');

        try {
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

            return new Image($gmagick);
        } catch (\GmagickException $e) {
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
        try
        {
            $gmagick = new \Gmagick();
            $gmagick->readimageblob($string);
            return new Image($gmagick);
        } catch(\GmagickException $e) {
            throw new RuntimeException(
                'Could not load image from string', $e->getCode(), $e
            );
        }
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImagineInterface::read()
     */
    public function read($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Variable does not contain a stream resource');
        }

        $content = stream_get_contents($resource);

        if (false === $content) {
            throw new InvalidArgumentException('Cannot read resource content');
        }

        return $this->load($content);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImagineInterface::font()
     */
    public function font($file, $size, Color $color)
    {
        $gmagick = new \Gmagick();

        $gmagick->newimage(1, 1, 'transparent');

        return new Font($gmagick, $file, $size, $color);
    }
}
