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

use Imagine\Box;
use Imagine\BoxInterface;
use Imagine\Color;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\ImageInterface;
use Imagine\Imagick\Imagine;
use Imagine\PointInterface;

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
     */
    public function __construct(\Imagick $imagick)
    {
        $this->imagick = $imagick;
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
     * @see Imagine\ImageInterface::copy()
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
        return new self($clone);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::crop()
     */
    public function crop(PointInterface $start, BoxInterface $size)
    {
        if (!$start->in($size)) {
            throw new OutOfBoundsException('Crop coordinates must start at '.
                'minimum 0, 0 position from top left corner, crop height and '.
                'width must be positive integers and must not exceed the '.
                'current image borders');
        }

        try {
            $this->imagick->cropImage(
                $size->getWidth(),
                $size->getHeight(),
                $start->getX(),
                $start->getY()
            );
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Crop operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::flipHorizontally()
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
     * @see Imagine\ImageInterface::flipVertically()
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
     * @see Imagine\ImageInterface::paste()
     */
    public function paste(ImageInterface $image, PointInterface $start)
    {
        $x = $start->getX();
        $y = $start->getY();

        if (!$image instanceof self) {
            throw new InvalidArgumentException(sprintf('Imagick\Image can '.
                'only paste() Imagick\Image instances, %s given',
                get_class($image)
            ));
        }

        if (!$this->getSize()->contains($image->getSize(), $start)) {
            throw new OutOfBoundsException(
                'Cannot paste image of the given size at the specified '.
                'position, as it moves outside of the current image\'s box'
            );
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
     * @see Imagine\ImageInterface::resize()
     */
    public function resize(BoxInterface $size)
    {
        try {
            $this->imagick->adaptiveResizeImage($size->getWidth(), $size->getHeight());
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Resize operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::rotate()
     */
    public function rotate($angle, Color $background = null)
    {
        $color = $background ? $background : new Color('fff');

        try {
            $pixel = $this->getColor($color);

            $this->imagick->rotateimage($pixel, $angle);

            $pixel->clear();
            $pixel->destroy();
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Rotate operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::save()
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
     * @see Imagine\ImageInterface::show()
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
     * @see Imagine\ImageInterface::thumbnail()
     */
    public function thumbnail(BoxInterface $size, $mode = ImageInterface::THUMBNAIL_INSET)
    {
        if ($mode !== ImageInterface::THUMBNAIL_INSET &&
            $mode !== ImageInterface::THUMBNAIL_OUTBOUND) {
            throw new InvalidArgumentException('Invalid mode specified');
        }

        $thumbnail = $this->copy();

        try {
            if ($mode === ImageInterface::THUMBNAIL_INSET) {
                $thumbnail->imagick->thumbnailImage(
                    $size->getWidth(),
                    $size->getHeight(),
                    true
                );
            } else if ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
                $thumbnail->imagick->cropThumbnailImage(
                    $size->getWidth(),
                    $size->getHeight()
                );
            }
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Thumbnail operation failed', $e->getCode(), $e
            );
        }

        return $thumbnail;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::draw()
     */
    public function draw()
    {
        return new Drawer($this->imagick);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::getSize()
     */
    public function getSize()
    {
        try {
            $width  = $this->imagick->getImageWidth();
            $height = $this->imagick->getImageHeight();
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Could not get size', $e->getCode(), $e
            );
        }

        return new Box($width, $height);
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

        $pixel->setColorValue(
            \Imagick::COLOR_OPACITY,
            number_format(abs(round($color->getAlpha() / 100, 1)), 1)
        );

        return $pixel;
    }
}