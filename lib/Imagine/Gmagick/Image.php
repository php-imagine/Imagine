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
use Imagine\Point;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\ImageInterface;
use Imagine\Gmagick\Imagine;

class Image implements ImageInterface
{
    /**
     * @var Gmagick
     */
    private $gmagick;

    /**
     * @var Imagine
     */
    private $imagine;

    /**
     * Constructs Image with Imagick and Imagine instances
     *
     * @param Gmagick $gmagick
     * @param Imagine $imagine
     */
    public function __construct(\Gmagick $gmagick, Imagine $imagine)
    {
        $this->gmagick = $gmagick;
        $this->imagine = $imagine;
    }

    /**
     * Destroys allocated imagick resources
     */
    public function __destruct()
    {
        if (null !== $this->gmagick && $this->gmagick instanceof \Gmagick) {
            $this->gmagick->clear();
            $this->gmagick->destroy();
        }
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::copy()
     */
    public function copy()
    {
        return new self(clone $this->gmagick, $this->imagine);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::crop()
     */
    public function crop(Point $start, $width, $height)
    {
        $x = $start->getX();
        $y = $start->getY();

        if ($x < 0 || $y < 0 || $width < 1 || $height < 1 ||
            $this->getWidth() - ($x + $width) < 0 ||
            $this->getHeight() - ($y + $height) < 0) {
            throw new OutOfBoundsException('Crop coordinates must start at '.
                'minimum 0, 0 position from top left corner, crop height and '.
                'width must be positive integers and must not exceed the '.
                'current image borders');
        }

        try {
            $this->gmagick->cropimage($width, $height, $x, $y);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Crop operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::flipHorizontally()
     */
    public function flipHorizontally()
    {
        try {
            $this->gmagick->flopimage();
        } catch (\GmagickException $e) {
            throw new RuntimeException('Horizontal flip operation failed',
                $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::flipVertically()
     */
    public function flipVertically()
    {
        try {
            $this->gmagick->flipimage();
        } catch (\GmagickException $e) {
            throw new RuntimeException('Vertical flip operation failed',
                $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::getHeight()
     */
    public function getHeight()
    {
        try {
            $height = $this->gmagick->getimageheight();
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Could not get height', $e->getCode(), $e
            );
        }

        return $height;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::getWidth()
     */
    public function getWidth()
    {
        try {
            $width = $this->gmagick->getimagewidth();
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Could not get width', $e->getCode(), $e
            );
        }

        return $width;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::paste()
     */
    public function paste(ImageInterface $image, Point $start)
    {
        $x = $start->getX();
        $y = $start->getY();

        if (!$image instanceof self) {
            throw new InvalidArgumentException(sprintf('Gmagick\Image can '.
                'only paste() Gmagick\Image instances, %s given',
                get_class($image)
            ));
        }

        $widthDiff = $this->getWidth() - ($x + $image->getWidth());
        $heightDiff = $this->getHeight() - ($y + $image->getHeight());

        if ($widthDiff < 0 || $heightDiff < 0) {
            throw new OutOfBoundsException(sprintf('Cannot paste image '.
                'of width %d and height %d at the x position of %d and y '.
                'position of %d, as it exceeds the parent image\'s width by '.
                '%d in width and %d in height', $image->getWidth(),
                $image->getHeight(), $x, $y, abs($widthDiff), abs($heightDiff)
            ));
        }

        try {
            $this->gmagick->compositeimage($image->gmagick, \Gmagick::COMPOSITE_DEFAULT, $x, $y);
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Paste operation failed', $e->getCode(), $e
            );
        }

        /**
         * @see http://pecl.php.net/bugs/bug.php?id=22435
         */
        if (method_exists($this->gmagick, 'flattenImages')) {
            try {
                $this->gmagick->flattenImages();
            } catch (\GmagickException $e) {
                throw new RuntimeException(
                    'Paste operation failed', $e->getCode(), $e
                );
            }
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::resize()
     */
    public function resize($width, $height)
    {
        if ($width < 1 || $height < 1) {
            throw new InvalidArgumentException('Width an height of the '.
                'resize must be positive integers');
        }

        try {
            $this->gmagick->resizeimage($width, $height,
                \Gmagick::FILTER_UNDEFINED, 1);
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Resize operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::rotate()
     */
    public function rotate($angle, Color $background = null)
    {
        try {
            $this->gmagick->rotateimage(
                $this->getColor($background), $angle);
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Rotate operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::save()
     */
    public function save($path, array $options = array())
    {
        try {
            $this->gmagick->writeimage($path);
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Save operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::show()
     */
    public function show($format, array $options = array())
    {
        try {
            $this->gmagick->setimageformat($format);
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Show operation failed', $e->getCode(), $e
            );
        }

        echo $this->gmagick;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::thumbnail()
     */
    public function thumbnail($width, $height, $mode = ImageInterface::THUMBNAIL_INSET)
    {
        if ($mode !== ImageInterface::THUMBNAIL_INSET &&
            $mode !== ImageInterface::THUMBNAIL_OUTBOUND) {
            throw new InvalidArgumentException('Invalid mode specified');
        }

        if ($width < 1 || $height < 1) {
            throw new InvalidArgumentException('Width an height of the '.
                'resize must be positive integers');
        }

        $inbound = ($mode == ImageInterface::THUMBNAIL_INSET) ? true : false;

        $thumbnail = $this->copy();
        try {
            $thumbnail->gmagick->thumbnailimage($width, $height, $inbound);
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Thumbnail operation failed', $e->getCode(), $e
            );
        }

        return $thumbnail;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::draw()
     */
    public function draw()
    {
    	// TODO: add drawing support to GMagick
        throw new RuntimeException('not implemented');
    }

    /**
     * Gets specifically formatted color string from Color instance
     *
     * @param Color $color
     *
     * @return string
     */
    protected function getColor(Color $color)
    {
        $pixel = new \GmagickPixel((string) $color);

        if ($color->getAlpha() > 0) {
            $opacity = number_format(abs(round($color->getAlpha() / 100, 1)), 1);
            $pixel->setColorValue(\Gmagick::COLOR_OPACITY, $opacity);
        }

        return $pixel;
    }
}
