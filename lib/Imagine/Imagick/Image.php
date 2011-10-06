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

use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\Fill\FillInterface;
use Imagine\Image\Fill\Gradient\Horizontal;
use Imagine\Image\Fill\Gradient\Linear;
use Imagine\Image\Fill\Gradient\Vertical;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Imagine\Image\ImageInterface;
use Imagine\Mask\MaskInterface;

final class Image implements ImageInterface
{
    /**
     * @var Imagick
     */
    private $imagick;

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
     * @see Imagine\Image\ManipulatorInterface::copy()
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
     * @see Imagine\Image\ManipulatorInterface::crop()
     */
    public function crop(PointInterface $start, BoxInterface $size)
    {
        if (!$start->in($this->getSize())) {
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
            // Reset canvas for gif format
            $this->imagick->setImagePage(0, 0, 0, 0);
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Crop operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::flipHorizontally()
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
     * @see Imagine\Image\ManipulatorInterface::flipVertically()
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
     * @see Imagine\Image\ManipulatorInterface::paste()
     */
    public function paste(ImageInterface $image, PointInterface $start)
    {
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
                $image->imagick, \Imagick::COMPOSITE_DEFAULT,
                $start->getX(),
                $start->getY()
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
     * @see Imagine\Image\ManipulatorInterface::resize()
     */
    public function resize(BoxInterface $size)
    {
        try {
            $this->imagick->thumbnailImage($size->getWidth(), $size->getHeight());
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Resize operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::rotate()
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
     * @see Imagine\Image\ManipulatorInterface::save()
     */
    public function save($path, array $options = array())
    {
        try {
            if (isset($options['format'])) {
                $this->imagick->setimageformat($options['format']);
            }

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
     * @see Imagine\Image\ManipulatorInterface::show()
     */
    public function show($format, array $options = array())
    {
        echo $this->get($format, $options);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::get()
     */
    public function get($format, array $options = array())
    {
        try {
            $this->applyImageOptions($this->imagick, $options);
            $this->imagick->setImageFormat($format);
        } catch (\ImagickException $e) {
            throw new InvalidArgumentException(
                'Show operation failed', $e->getCode(), $e
            );
        }

        return (string) $this->imagick;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::__toString()
     */
    public function __toString()
    {
        return $this->get('png');
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::thumbnail()
     */
    public function thumbnail(BoxInterface $size, $mode = ImageInterface::THUMBNAIL_INSET)
    {
        if ($mode !== ImageInterface::THUMBNAIL_INSET &&
            $mode !== ImageInterface::THUMBNAIL_OUTBOUND) {
            throw new InvalidArgumentException('Invalid mode specified');
        }

        $width     = $size->getWidth();
        $height    = $size->getHeight();
        $thumbnail = $this->copy();

        try {
            if ($mode === ImageInterface::THUMBNAIL_INSET) {
                $thumbnail->imagick->thumbnailImage(
                    $width,
                    $height,
                    true
                );
            } else if ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
                $thumbnail->imagick->cropThumbnailImage(
                    $width,
                    $height
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
     * @see Imagine\Image\ImageInterface::draw()
     */
    public function draw()
    {
        return new Drawer($this->imagick);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::getSize()
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
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::applyMask()
     */
    public function applyMask(ImageInterface $mask)
    {
        if (!$mask instanceof self) {
            throw new InvalidArgumentException(
                'Can only apply instances of Imagine\Imagick\Image as masks'
            );
        }

        $size = $this->getSize();
        $maskSize = $mask->getSize();

        if ($size != $maskSize) {
            throw new InvalidArgumentException(sprintf(
                'The given mask doesn\'t match current image\'s size, Current '.
                'mask\'s dimensions are %s, while image\'s dimensions are %s',
                $maskSize, $size
            ));
        }

        $mask = $mask->mask();

        $mask->imagick->negateImage(true);

        try {
            $this->imagick->compositeImage(
                $mask->imagick,
                \Imagick::COMPOSITE_COPYOPACITY,
                0, 0
            );

            $mask->imagick->clear();
            $mask->imagick->destroy();
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Apply mask operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::mask()
     */
    public function mask()
    {
        $mask = $this->copy();

        try {
            $mask->imagick->modulateImage(100, 0, 100);
            $mask->imagick->setImageMatte(false);
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Mask operation failed', $e->getCode(), $e
            );
        }

        return $mask;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::fill()
     */
    public function fill(FillInterface $fill)
    {
        try {
            if ($this->isLinearOpaque($fill)) {
                $this->applyFastLinear($fill);
            } else {
                $iterator = $this->imagick->getPixelIterator();

                foreach ($iterator as $y => $pixels) {
                    foreach ($pixels as $x => $pixel) {
                        $color = $fill->getColor(new Point($x, $y));

                        $pixel->setColor((string) $color);
                        $pixel->setColorValue(
                            \Imagick::COLOR_OPACITY,
                            number_format(abs(round($color->getAlpha() / 100, 1)), 1)
                        );
                    }

                    $iterator->syncIterator();
                }
            }
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Fill operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::histogram()
     */
    public function histogram()
    {
        $pixels = $this->imagick->getImageHistogram();

        return array_map(
            function(\ImagickPixel $pixel)
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
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::getColorAt()
     */
    public function getColorAt(PointInterface $point) {
        if(!$point->in($this->getSize())) {
            throw new RuntimeException(sprintf(
                'Error getting color at point [%s,%s]. The point must be inside the image of size [%s,%s]',
                $point->getX(), $point->getY(), $this->getSize()->getWidth(), $this->getSize()->getHeight()
            ));
        }
        $pixel = $this->imagick->getImagePixelColor($point->getX(), $point->getY());
        return new Color(array(
                $pixel->getColorValue(\Imagick::COLOR_RED) * 255,
                $pixel->getColorValue(\Imagick::COLOR_GREEN) * 255,
                $pixel->getColorValue(\Imagick::COLOR_BLUE) * 255,
            ),
            (int) round($pixel->getColorValue(\Imagick::COLOR_ALPHA) * 100)
        );
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
     * @param Imagine\Image\Color $color
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

    /**
     * Checks whether given $fill is linear and opaque
     *
     * @param Imagine\Image\Fill\FillInterface $fill
     *
     * @return Boolean
     */
    private function isLinearOpaque(FillInterface $fill)
    {
        return $fill instanceof Linear &&
               ($fill->getStart()->isOpaque() && $fill->getEnd()->isOpaque());
    }

    /**
     * Performs optimized gradient fill for non-opaque linear gradients
     *
     * @param Imagine\Image\Fill\Gradient\Linear $fill
     */
    private function applyFastLinear(Linear $fill)
    {
        $gradient = new \Imagick();
        $size     = $this->getSize();
        $color    = sprintf(
            'gradient:%s-%s',
            (string) $fill->getStart(),
            (string) $fill->getEnd()
        );

        if ($fill instanceof Horizontal) {
            $gradient->newPseudoImage(
                $size->getHeight(),
                $size->getWidth(),
                $color
            );

            $gradient->rotateImage(new \ImagickPixel(), 90);
        } else {
            $gradient->newPseudoImage(
                $size->getWidth(),
                $size->getHeight(),
                $color
            );
        }

        $this->imagick->compositeImage(
            $gradient,
            \Imagick::COMPOSITE_OVER,
            0,
            0
        );

        $gradient->clear();
        $gradient->destroy();
    }
}
