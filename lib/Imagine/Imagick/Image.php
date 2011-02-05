<?php

namespace Imagine\Imagick;

use Imagine\Imagick\ImageFactory;
use Imagine\Color;

use Imagine\Exception\OutOfBoundsException;

use Imagine\Exception\InvalidArgumentException;

use Imagine\Exception\RuntimeException;

use Imagine\ImageInterface;

class Image implements ImageInterface
{
    /**
     * @var Imagick
     */
    private $imagick;

    /**
     * @var ImageFactory
     */
    private $factory;

    public function __construct(\Imagick $imagick, ImageFactory $factory)
    {
        $this->imagick = $imagick;
        $this->factory = $factory;
    }

    public function __destruct()
    {
        if (null !== $this->imagick && $this->imagick instanceof \Imagick) {
            $this->imagick->clear();
            $this->imagick->destroy();
        }
    }

    public function copy()
    {
        return new self($this->imagick->clone(), $this->factory);
    }

    public function crop($x, $y, $width, $height)
    {
        if ($x < 0 || $y < 0 || $width < 1 || $height < 1 ||
            $this->getWidth() - ($x + $width) < 0 ||
            $this->getHeight() - ($y + $height) < 0) {
            throw new OutOfBoundsException('Crop coordinates must start at '.
                'minimum 0, 0 position from top left corner, crop height and '.
                'width must be positive integers and must not exceed the '.
                'current image borders');
        }

        if (false === $this->imagick->cropImage($width, $height, $x, $y)) {
            throw new RuntimeException('Crop operation failed');
        }

        return $this;
    }

    public function flipHorizontally()
    {
        if (false === $this->imagick->flopImage()) {
            throw new RuntimeException('Horizontal Flip operation failed');
        }

        return $this;
    }

    public function flipVertically()
    {
        if (false === $this->imagick->flipImage()) {
            throw new RuntimeException('Vertical flip operation failed');
        }

        return $this;
    }

    public function getHeight()
    {
        try {
            $height = $this->imagick->getImageHeight();
        } catch (\ImagickException $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $height;
    }

    public function getWidth()
    {
        try {
            $width = $this->imagick->getImageWidth();
        } catch (\ImagickException $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $width;
    }

    public function paste(ImageInterface $image, $x, $y)
    {
        if (!$image instanceof self) {
            throw new InvalidArgumentException(sprintf('Imagick\Image can '.
                'only paste() Gd\Image instances, %s given', get_class($image)
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

        if (false === $this->imagick->compositeImage($image->imagick, \Imagick::COMPOSITE_DEFAULT, $x, $y)) {
            throw new RuntimeException('Paste operation failed');
        }

        $this->imagick->flattenImages();

        return $this;
    }

    public function resize($width, $height)
    {
        if ($width < 1 || $height < 1) {
            throw new InvalidArgumentException('Width an height of the '.
                'resize must be positive integers');
        }

        if (false === $this->imagick->adaptiveResizeImage($width, $height)) {
            throw new RuntimeException('Resize operation failed');
        }

        return $this;
    }

    public function rotate($angle, Color $background = null)
    {
        if (false === $this->imagick->rotateimage(
            $this->getColor($background), $angle)) {
            throw new RuntimeException('Rotate operation failed');
        }

        return $this;
    }

    public function save($path, array $options = array())
    {
        if (false === $this->imagick->writeImage($path)) {
            throw new RuntimeException('Save operation failed');
        }

        return $this;
    }

    public function show($format, array $options = array())
    {
        if (false === $this->imagick->setImageFormat($format)) {
            throw new InvalidArgumentException('Usupported format specified');
        }

        echo $this->imagick;

        return $this;
    }

    public function thumbnail($width, $height, $mode = ImageInterface::THUMBNAIL_INSET, Color $background = null)
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
            if (false === $thumbnail->imagick->thumbnailImage($width, $height, true)) {
                throw new RuntimeException('Thumbnail operation failed');
            }

            $canvas = $this->factory->create($width, $height, $background);
            $x      = abs(round(($width - $thumbnail->getWidth()) / 2));
            $y      = abs(round(($height - $thumbnail->getHeight()) / 2));

            $thumbnail = $canvas->paste($thumbnail,$x, $y);
        } else if ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
            if (false === $thumbnail->imagick->cropThumbnailImage($width, $height)) {
                throw new RuntimeException('Thumbnail operation failed');
            }
        }

        return $thumbnail;
    }

    protected function getColor(Color $color)
    {
        return new \ImagickPixel(sprintf('rgba(%d,%d,%d,%d)',
            $color->getRed(), $color->getGreen(), $color->getBlue(),
            round($color->getAlpha() / 100, 1)));
    }
}