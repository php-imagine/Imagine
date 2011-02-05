<?php

namespace Imagine\Imagick;

use Imagine\Color;
use Imagine\Exception\InvalidArgumentException;
use Imagine\ImageFactoryInterface;

class ImageFactory implements ImageFactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see Imagine.ImageFactoryInterface::open()
     */
    public function open($path)
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf('File %s doesn\'t '.
                'exist', $path));
        }

        return new Image(new \Imagick($path), $this);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageFactoryInterface::create()
     */
    public function create($width, $height, Color $color = null)
    {
        if ($width < 1 || $height < 1) {
            throw new InvalidArgumentException('Width an height of the '.
                'resize must be positive integers');
        }

        $color = null !== $color ? $color : new Color('fff');

        $imagick = new \Imagick();
        $imagick->newImage($width, $height, $this->getColor($color));

        return new Image($imagick, $this);
    }

    protected function getColor(Color $color)
    {
        return new \ImagickPixel(sprintf('rgba(%d,%d,%d,%d)',
            $color->getRed(), $color->getGreen(), $color->getBlue(),
            abs(1 - round($color->getAlpha() / 100, 1))
        ));
    }
}
