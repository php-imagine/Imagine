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
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Imagine\Image\ImageInterface;

/**
 * Image implementation using the Imagick PHP extension
 */
final class Image implements ImageInterface
{
    /**
     * @var \Imagick
     */
    private $imagick;
    /**
     * @var Layers
     */
    private $layers;

    /**
     * Constructs Image with Imagick and Imagine instances
     *
     * @param \Imagick $imagick
     */
    public function __construct(\Imagick $imagick)
    {
        $this->imagick = $imagick;
        $this->layers = new Layers($this, $this->imagick);
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
     * Returns imagick instance
     *
     * @return Imagick
     */
    public function getImagick()
    {
        return $this->imagick;
    }

    /**
     * {@inheritdoc}
     */
    public function copy()
    {
        try {
            if (version_compare(phpversion("imagick"), "3.1.0b1", ">=")) {
                $clone = clone $this->imagick;
            } else {
                $clone = $this->imagick->clone();
            }
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Copy operation failed', $e->getCode(), $e
            );
        }

        return new self($clone);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function flipHorizontally()
    {
        try {
            $this->imagick->flopImage();
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Horizontal Flip operation failed', $e->getCode(), $e
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
            $this->imagick->flipImage();
        } catch (\ImagickException $e) {
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
            $this->imagick->stripImage();
        } catch (\ImagickException $e) {
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resize(BoxInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        static $supportedFilters = array(
            ImageInterface::FILTER_UNDEFINED => \Imagick::FILTER_UNDEFINED,
            ImageInterface::FILTER_BESSEL    => \Imagick::FILTER_BESSEL,
            ImageInterface::FILTER_BLACKMAN  => \Imagick::FILTER_BLACKMAN,
            ImageInterface::FILTER_BOX       => \Imagick::FILTER_BOX,
            ImageInterface::FILTER_CATROM    => \Imagick::FILTER_CATROM,
            ImageInterface::FILTER_CUBIC     => \Imagick::FILTER_CUBIC,
            ImageInterface::FILTER_GAUSSIAN  => \Imagick::FILTER_GAUSSIAN,
            ImageInterface::FILTER_HANNING   => \Imagick::FILTER_HANNING,
            ImageInterface::FILTER_HAMMING   => \Imagick::FILTER_HAMMING,
            ImageInterface::FILTER_HERMITE   => \Imagick::FILTER_HERMITE,
            ImageInterface::FILTER_LANCZOS   => \Imagick::FILTER_LANCZOS,
            ImageInterface::FILTER_MITCHELL  => \Imagick::FILTER_MITCHELL,
            ImageInterface::FILTER_POINT     => \Imagick::FILTER_POINT,
            ImageInterface::FILTER_QUADRATIC => \Imagick::FILTER_QUADRATIC,
            ImageInterface::FILTER_SINC      => \Imagick::FILTER_SINC,
            ImageInterface::FILTER_TRIANGLE  => \Imagick::FILTER_TRIANGLE
        );

        if (!array_key_exists($filter, $supportedFilters)) {
            throw new InvalidArgumentException('Unsupported filter type');
        }

        try {
            $this->imagick->resizeImage(
                $size->getWidth(),
                $size->getHeight(),
                $supportedFilters[$filter],
                1
            );
        } catch (\ImagickException $e) {
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
     * {@inheritdoc}
     */
    public function save($path, array $options = array())
    {
        try {
            $this->prepareOutput($options);
            $this->imagick->writeImages($path, true);
        } catch (\ImagickException $e) {
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
            $options["format"] = $format;
            $this->prepareOutput($options);
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Get operation failed', $e->getCode(), $e
            );
        }

        return $this->imagick->getImagesBlob();
    }

    /**
     * {@inheritdoc}
     **/
    public function interlace($scheme)
    {
        static $supportedInterlaceSchemes = array(
            ImageInterface::INTERLACE_NONE      => \Imagick::INTERLACE_NO,
            ImageInterface::INTERLACE_LINE      => \Imagick::INTERLACE_LINE,
            ImageInterface::INTERLACE_PLANE     => \Imagick::INTERLACE_PLANE,
            ImageInterface::INTERLACE_PARTITION => \Imagick::INTERLACE_PARTITION,
        );

        if (!array_key_exists($scheme, $supportedInterlaceSchemes)) {
            throw new InvalidArgumentException('Unsupported interlace type');
        }

        $this->imagick->setInterlaceScheme($supportedInterlaceSchemes[$scheme]);

        return $this;
    }

    /**
     * @param array $options
     */
    private function prepareOutput(array $options)
    {
        if (isset($options['format'])) {
            $this->imagick->setImageFormat($options['format']);
        }

        if (isset($options['animated']) && true === $options['animated']) {

            $format = isset($options['format']) ? $options['format'] : 'gif';
            $delay = isset($options['animated.delay']) ? $options['animated.delay'] : 800;
            $loops = isset($options['animated.loops']) ? $options['animated.loops'] : 0;

            $options['flatten'] = false;

            $this->layers->animate($format, $delay, $loops);
        } else {
            $this->layers->merge();
        }
        $this->applyImageOptions($this->imagick, $options);

        // flatten only if image has multiple layers
        if ((!isset($options['flatten']) || $options['flatten'] === true)
            && count($this->layers) > 1) {
            $this->flatten();
        }
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

        $imageSize = $this->getSize();
        $thumbnail = $this->copy();

        // if target width is larger than image width
        // AND target height is longer than image height
        if ($size->contains($imageSize)) {
            return $thumbnail;
        }

        // if target width is larger than image width
        // OR target height is longer than image height
        if (!$imageSize->contains($size)) {
            $size = new Box(
                min($imageSize->getWidth(), $size->getWidth()),
                min($imageSize->getHeight(), $size->getHeight())
            );
        }

        try {
            if ($mode === ImageInterface::THUMBNAIL_INSET) {
                $thumbnail->imagick->thumbnailImage(
                    $size->getWidth(),
                    $size->getHeight(),
                    true
                );
            } elseif ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
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
     * {@inheritdoc}
     */
    public function draw()
    {
        return new Drawer($this->imagick);
    }

    /**
     * {@inheritdoc}
     */
    public function effects()
    {
        return new Effects($this->imagick);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
            // remove transparent areas of the original from the mask
            $mask->imagick->compositeImage(
                $this->imagick,
                \Imagick::COMPOSITE_DSTIN,
                0, 0
            );

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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function histogram()
    {
        $pixels = $this->imagick->getImageHistogram();

        return array_map(
            function(\ImagickPixel $pixel) {
                $info = $pixel->getColor();
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
        try {
            $this->imagick = $this->imagick->flattenImages();
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Flatten operation failed', $e->getCode(), $e
            );
        }
    }

    /**
     * Internal
     *
     * Applies options before save or output
     *
     * @param \Imagick $image
     * @param array    $options
     */
    private function applyImageOptions(\Imagick $image, array $options)
    {
        if (isset($options['quality'])) {
            $image->setImageCompressionQuality($options['quality']);
        }

        if(isset($options['resolution-units']) && isset($options['resolution-x'])
          && isset($options['resolution-y'])) {

            if ($options['resolution-units'] == ImageInterface::RESOLUTION_PIXELSPERCENTIMETER) {
                $image->setImageUnits(\Imagick::RESOLUTION_PIXELSPERCENTIMETER);
            } elseif ($options['resolution-units'] == ImageInterface::RESOLUTION_PIXELSPERINCH) {
                $image->setImageUnits(\Imagick::RESOLUTION_PIXELSPERINCH);
            } else {
                throw new RuntimeException('Unsupported image unit format');
            }

            $image->setImageResolution($options['resolution-x'], $options['resolution-y']);
            $image->resampleImage($options['resolution-x'], $options['resolution-y'], \Imagick::FILTER_UNDEFINED, 0);
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

    /**
     * Checks whether given $fill is linear and opaque
     *
     * @param FillInterface $fill
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
     * @param Linear $fill
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
