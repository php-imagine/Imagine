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
use Imagine\Point;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\ImageInterface;
use Imagine\Imagick\Imagine;

final class Image implements ImageInterface
{
    /**
     * @var Imagick
     */
    private $imagick;

    /**
     * @var Imagine
     */
    private $imagine;

    /**
     * Constructs Image with Imagick and Imagine instances
     *
     * @param Imagick $imagick
     * @param Imagine $imagine
     */
    public function __construct(\Imagick $imagick, Imagine $imagine)
    {
        $this->imagick = $imagick;
        $this->imagine = $imagine;
    }

    /**
     * Destroys allocated imagick resources
     */
    public function __destruct()
    {
        if (null !== $this->imagick && $this->imagick instanceof \Imagick) {
            $this->imagick->clear();
            $this->imagick->destroy();
        }
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::copy()
     */
    public function copy()
    {
        try {
            $clone = $this->imagick->clone();
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Copy operation failed', $e->getCode(), $e
            );
        }
        return new self($clone, $this->imagine);
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
            $this->imagick->cropImage($width, $height, $x, $y);
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Crop operation failed', $e->getCode(), $e
            );
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
            $this->imagick->flopImage();
        }
        catch (\ImagickException $e) {
            throw new RuntimeException(
                'Horizontal Flip operation failed', $e->getCode(), $e
            );
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
            $this->imagick->flipImage();
        }
        catch (\ImagickException $e) {
            throw new RuntimeException(
                'Vertical flip operation failed', $e->getCode(), $e
            );
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
            $height = $this->imagick->getImageHeight();
        } catch (\ImagickException $e) {
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
            $width = $this->imagick->getImageWidth();
        } catch (\ImagickException $e) {
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
            throw new InvalidArgumentException(sprintf('Imagick\Image can '.
                'only paste() Imagick\Image instances, %s given',
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
            $this->imagick->compositeImage(
                $image->imagick, \Imagick::COMPOSITE_DEFAULT, $x, $y
            );
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Paste operation failed', $e->getCode(), $e
            );
        }

        try {
            $this->imagick->flattenImages();
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Paste operation failed', $e->getCode(), $e
            );
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
            $this->imagick->adaptiveResizeImage($width, $height);
        } catch (\ImagickException $e) {
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
            $this->imagick->rotateimage($this->getColor($background), $angle);
        } catch (\ImagickException $e) {
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
            $this->applyImageOptions($this->imagick, $options);
            $this->imagick->writeImage($path);
        } catch (\ImagickException $e) {
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
            $this->applyImageOptions($this->imagick, $options);
            $this->imagick->setImageFormat($format);
        } catch (\ImagickException $e) {
            throw new InvalidArgumentException(
                'Show operation failed', $e->getCode(), $e
            );
        }

        echo $this->imagick;

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

        $thumbnail = $this->copy();

        if ($mode === ImageInterface::THUMBNAIL_INSET) {
            try {
                $thumbnail->imagick->thumbnailImage($width, $height, true);
            } catch (\ImagickException $e) {
                throw new RuntimeException(
                    'Thumbnail operation failed', $e->getCode(), $e
                );
            }
        } else if ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
            try {
                $thumbnail->imagick->cropThumbnailImage($width, $height);
            } catch (\ImagickException $e) {
                throw new RuntimeException(
                    'Thumbnail operation failed', $e->getCode(), $e
                );
            }
        }

        return $thumbnail;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::draw()
     */
    public function draw()
    {
        return new Drawer($this->imagick);
    }

    /**
     * Internal
     *
     * Applies options before save or output
     *
     * @param \Imagick $image
     * @param array $options
     */
    private function applyImageOptions(\Imagick $image, array $options)
    {
        if (isset($options['quality'])) {
            $image->setImageCompressionQuality($options['quality']);
        }
    }

    /**
     * Gets specifically formatted color string from Color instance
     *
     * @param Color $color
     *
     * @return string
     */
    private function getColor(Color $color)
    {
        $pixel = new \ImagickPixel((string) $color);
        if ($color->getAlpha() > 0) {
            $opacity = number_format(abs(round($color->getAlpha() / 100, 1)), 1);
            $pixel->setColorValue(\Imagick::COLOR_OPACITY, $opacity);
        }

        return $pixel;
    }
}