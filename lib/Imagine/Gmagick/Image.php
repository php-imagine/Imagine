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
use Imagine\PointInterface;
use Imagine\Box;
use Imagine\BoxInterface;
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
     * Constructs Image with Gmagick and Imagine instances
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
     * Destroys allocated gmagick resources
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
    public function crop(PointInterface $start, BoxInterface $size)
    {
        if (!$start->in($size)) {
            throw new OutOfBoundsException(
                'Crop coordinates must start at minimum 0, 0 position from '.
                'top left corner, crop height and width must be positive '.
                'integers and must not exceed the current image borders'
            );
        }

        $width  = $size->getWidth();
        $height = $size->getHeight();
        $x      = $start->getX();
        $y      = $start->getY();

        try {
            $this->gmagick->cropimage($width, $height, $x, $y);
        } catch (\GmagickException $e) {
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
            $this->gmagick->flopimage();
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Horizontal flip operation failed', $e->getCode(), $e
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
            $this->gmagick->flipimage();
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Vertical flip operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::paste()
     */
    public function paste(ImageInterface $image, PointInterface $start)
    {
        $x = $start->getX();
        $y = $start->getY();

        if (!$image instanceof self) {
            throw new InvalidArgumentException(sprintf(
                'Gmagick\Image can only paste() Gmagick\Image instances, '.
                '%s given', get_class($image)
            ));
        }

        if (!$this->getSize()->contains($image->getSize(), $start)) {
            throw new OutOfBoundsException(
                'Cannot paste image of the given size at the specified '.
                'position, as it moves outside of the current image\'s box'
            );
        }

        try {
            $this->gmagick->compositeimage(
                $image->gmagick,
                \Gmagick::COMPOSITE_DEFAULT,
                $x, $y
            );
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
    public function resize(BoxInterface $size)
    {
        try {
            $this->gmagick->resizeimage(
                $size->getWidth(),
                $size->getHeight(),
                \Gmagick::FILTER_UNDEFINED,
                1
            );
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
                $this->getColor($background),
                $angle
            );
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
    public function thumbnail(BoxInterface $size, $mode = ImageInterface::THUMBNAIL_INSET)
    {
        if ($mode !== ImageInterface::THUMBNAIL_INSET &&
            $mode !== ImageInterface::THUMBNAIL_OUTBOUND) {
            throw new InvalidArgumentException('Invalid mode specified');
        }

        $thumbnail = $this->copy();

        try {
            if ($mode === ImageInterface::THUMBNAIL_INSET) {
                $thumbnail->gmagick->thumbnailimage(
                    $size->getWidth(),
                    $size->getHeight(),
                    true
                );
            } elseif ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
                $thumbnail->gmagick->cropthumbnailimage(
                    $size->getWidth(),
                    $size->getHeight()
                );
            }
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
        return new Drawer($this->gmagick);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::getSize()
     */
    public function getSize()
    {
        return new Box(
            $this->gmagick->getimagewidth(),
            $this->gmagick->getimageheight()
        );
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

        $pixel->setColorValue(
            \Gmagick::COLOR_OPACITY,
            number_format(abs(round($color->getAlpha() / 100, 1)), 1)
        );

        return $pixel;
    }
}
