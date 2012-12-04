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
use Imagine\Gmagick\Imagine;
use Imagine\Image\ImageInterface;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\Fill\FillInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

/**
 * Image implementation using the Gmagick PHP extension
 */
class Image implements ImageInterface
{
    /**
     * @var \Gmagick
     */
    private $gmagick;
    /**
     * @var Layers
     */
    private $layers;

    /**
     * Constructs Image with Gmagick and Imagine instances
     *
     * @param \Gmagick $gmagick
     */
    public function __construct(\Gmagick $gmagick)
    {
        $this->gmagick = $gmagick;
        $this->layers = new Layers($this, $this->gmagick);
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
     * {@inheritdoc}
     */
    public function copy()
    {
        return new self(clone $this->gmagick);
    }

    /**
     * {@inheritdoc}
     */
    public function crop(PointInterface $start, BoxInterface $size)
    {
        if (!$start->in($this->getSize())) {
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function strip()
    {
        try {
            $this->gmagick->stripimage();
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Strip operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * {@inheritdoc}
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

        $this->flatten();

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function rotate($angle, Color $background = null)
    {
        try {
            $background = $background ?: new Color('fff');
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
     * Internal
     *
     * Applies options before save or output
     *
     * @param \Gmagick $image
     * @param array    $options
     */
    private function applyImageOptions(\Gmagick $image, array $options)
    {
        if (isset($options['quality'])) {
            $image->setCompressionQuality($options['quality']);
        }

        if(isset($options['resolution-units']) && isset($options['resolution-x'])
          && isset($options['resolution-y'])) {

            if ($options['resolution-units'] == ImageInterface::RESOLUTION_PIXELSPERCENTIMETER) {
                $image->setimageunits(\Gmagick::RESOLUTION_PIXELSPERCENTIMETER);
            } elseif ($options['resolution-units'] == ImageInterface::RESOLUTION_PIXELSPERINCH) {
                $image->setimageunits(\Gmagick::RESOLUTION_PIXELSPERINCH);
            } else {
                throw new RuntimeException('Unsupported image unit format');
            }

            $image->setimageresolution($options['resolution-x'], $options['resolution-y']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save($path, array $options = array())
    {
        try {
            if (isset($options['format'])) {
                $this->gmagick->setimageformat($options['format']);
            }

            $this->layers()->merge();
            $this->applyImageOptions($this->gmagick, $options);

            // flatten only if image has multiple layers
            if ((!isset($options['flatten']) || $options['flatten'] === true)
                && count($this->layers()) > 1) {
                $this->flatten();
            }

            $this->gmagick->writeimage($path);
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Save operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function show($format, array $options = array())
    {
        header('Content-type: '.$this->getMimeType($format));
        echo $this->get($format, $options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($format, array $options = array())
    {
        try {
            $this->applyImageOptions($this->gmagick, $options);
            $this->gmagick->setimageformat($format);
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Show operation failed', $e->getCode(), $e
            );
        }

        return (string) $this->gmagick;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->get('png');
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function draw()
    {
        return new Drawer($this->gmagick);
    }

    /**
     * {@inheritdoc}
     */
    public function effects()
    {
        return new Effects($this->gmagick);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
                'The given mask doesn\'t match current image\'s size, current '.
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function histogram()
    {
        $pixels = $this->gmagick->getimagehistogram();

        return array_map(
            function(\GmagickPixel $pixel) {
                $info = $pixel->getColor(true);
                $opacity = isset($infos['a']) ? $info['a'] : 0;

                return new Color(
                    array(
                        $info['r'],
                        $info['g'],
                        $info['b'],
                    ),
                    (int) round($opacity * 100)
                );
            },
            $pixels
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getColorAt(PointInterface $point)
    {
        if (!$point->in($this->getSize())) {
            throw new RuntimeException(sprintf(
                'Error getting color at point [%s,%s]. The point must be inside the image of size [%s,%s]',
                $point->getX(), $point->getY(), $this->getSize()->getWidth(), $this->getSize()->getHeight()
            ));
        }

        try {
            $cropped   = clone $this->gmagick;
            $histogram = $cropped->cropImage(1, 1, $point->getX(), $point->getY())
                ->getImageHistogram();
        } catch (\GmagickException $e) {
            throw new RuntimeException('Unable to get the pixel');
        }

        $pixel = array_shift($histogram);

        unset($histogram, $cropped);

        return new Color(array(
                $pixel->getColorValue(\Gmagick::COLOR_RED) * 255,
                $pixel->getColorValue(\Gmagick::COLOR_GREEN) * 255,
                $pixel->getColorValue(\Gmagick::COLOR_BLUE) * 255,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function layers()
    {
        return $this->layers;
    }


    /**
     * Internal
     *
     * Flatten the image.
     */
    private function flatten()
    {
        /**
         * @see http://pecl.php.net/bugs/bug.php?id=22435
         */
        if (method_exists($this->gmagick, 'flattenImages')) {
            try {
                $this->gmagick = $this->gmagick->flattenImages();
            } catch (\GmagickException $e) {
                throw new RuntimeException(
                    'Flatten operation failed', $e->getCode(), $e
                );
            }
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
        if (!$color->isOpaque()) {
            throw new InvalidArgumentException('Gmagick doesn\'t support transparency');
        }

        $pixel = new \GmagickPixel((string) $color);

        $pixel->setColorValue(
            \Gmagick::COLOR_OPACITY,
            number_format(abs(round($color->getAlpha() / 100, 1)), 1)
        );

        return $pixel;
    }

    /**
     * Internal
     *
     * Get the mime type based on format.
     *
     * @param string $format
     *
     * @return string mime-type
     *
     * @throws RuntimeException
     */
    private function getMimeType($format)
    {
        static $mimeTypes = array(
            'jpeg' => 'image/jpeg',
            'jpg'  => 'image/jpeg',
            'gif'  => 'image/gif',
            'png'  => 'image/png',
            'wbmp' => 'image/vnd.wap.wbmp',
            'xbm'  => 'image/xbm',
        );

        if (!isset($mimeTypes[$format])) {
            throw new RuntimeException(sprintf(
                'Unsupported format given. Only %s are supported, %s given',
                implode(", ", array_keys($mimeTypes)), $format
            ));
        }

        return $mimeTypes[$format];
    }
}
