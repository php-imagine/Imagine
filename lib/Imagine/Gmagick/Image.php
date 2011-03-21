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

use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Fill\FillInterface;
use Imagine\Gmagick\Imagine;
use Imagine\ImageInterface;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class Image implements ImageInterface
{
    /**
     * @var Gmagick
     */
    private $gmagick;

    /**
     * Constructs Image with Gmagick and Imagine instances
     *
     * @param Gmagick $gmagick
     */
    public function __construct(\Gmagick $gmagick)
    {
        $this->gmagick = $gmagick;
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
     * @see Imagine\ImageInterface::copy()
     */
    public function copy()
    {
        return new self(clone $this->gmagick);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::crop()
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

        try {
            $this->gmagick->cropimage(
                $size->getWidth(),
                $size->getHeight(),
                $start->getX(),
                $start->getY()
            );
        } catch (\GmagickException $e) {
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
     * @see Imagine\ImageInterface::flipVertically()
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
     * @see Imagine\ImageInterface::paste()
     */
    public function paste(ImageInterface $image, PointInterface $start)
    {
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
                $start->getX(),
                $start->getY()
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
     * @see Imagine\ImageInterface::resize()
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
     * @see Imagine\ImageInterface::rotate()
     */
    public function rotate($angle, Color $background = null)
    {
        try {
            $pixel = $this->getColor($background);

            $this->gmagick->rotateimage($pixel, $angle);

            $pixel = null;
        } catch (\GmagickException $e) {
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
     * @see Imagine\ImageInterface::show()
     */
    public function show($format, array $options = array())
    {
        echo $this->get($format, $options);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::get()
     */
    public function get($format, array $options = array())
    {
        try {
            $this->gmagick->setimageformat($format);
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Show operation failed', $e->getCode(), $e
            );
        }

        return (string) $this->gmagick;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::__toString()
     */
    public function __toString()
    {
        return $this->get('png');
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
     * @see Imagine\ImageInterface::draw()
     */
    public function draw()
    {
        return new Drawer($this->gmagick);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::getSize()
     */
    public function getSize()
    {
        try {
            $width  = $this->gmagick->getimagewidth();
            $height = $this->gmagick->getimageheight();
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Get size operation failed', $e->getCode(), $e
            );
        }
        return new Box($width, $height);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::applyMask()
     */
    public function applyMask(ImageInterface $mask)
    {
        if (!$mask instanceof self) {
            throw new InvalidArgumentException(
                'Can only apply instances of Imagine\Gmagick\Image as masks'
            );
        }

        $size = $this->getSize();
        $maskSize = $mask->getSize();

        if ($size != $maskSize) {
            throw new InvalidArgumentException(sprintf(
                'The given mask doesn\'t match current image\'s sise, current '.
                'mask\'s dimensions are %s, while image\'s dimensions are %s',
                $maskSize, $size
            ));
        }

        try {
            $mask = $mask->copy();

            $this->gmagick->compositeimage(
                $mask->gmagick,
                \Gmagick::COMPOSITE_DEFAULT,
                0, 0
            );
        } catch (\Exception $e) {
            throw new RuntimeException(
                'Apply mask operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::mask()
     */
    public function mask()
    {
        $mask = $this->copy();

        try {
            $mask->gmagick->modulateimage(100, 0, 100);
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Mask operation failed', $e->getCode(), $e
            );
        }

        return $mask;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::fill()
     */
    public function fill(FillInterface $fill)
    {
        try {
            $draw = new \GmagickDraw();
            $size = $this->getSize();

            for ($x = 0; $x <= $size->getWidth(); $x++) {
                for ($y = 0; $y <= $size->getHeight(); $y++) {
                    $pixel = $this->getColor($fill->getColor(new Point($x, $y)));

                    $draw->setfillcolor($pixel);
                    $draw->point($x, $y);

                    $pixel = null;
                }
            }

            $this->gmagick->drawimage($draw);

            $draw = null;
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Fill operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::histogram()
     */
    public function histogram()
    {
        $pixels = $this->gmagick->getimagehistogram();

        return array_map(
            function(\GmagickPixel $pixel)
            {
                $info = $pixel->getColor();
                return new Color(
                    array(
                        $info['r'],
                        $info['g'],
                        $info['b'],
                    ),
                    (int) round($info['a'] * 100)
                );
            },
            $pixels
        );
    }

    /**
     * Gets specifically formatted color string from Color instance
     *
     * @param Imagine\Image\Color $color
     *
     * @return string
     */
    private function getColor(Color $color)
    {
        $pixel = new \GmagickPixel((string) $color);

        $pixel->setColorValue(
            \Gmagick::COLOR_OPACITY,
            number_format(abs(round($color->getAlpha() / 100, 1)), 1)
        );

        return $pixel;
    }
}
