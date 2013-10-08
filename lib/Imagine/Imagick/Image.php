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
use Imagine\Image\AbstractImage;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Fill\FillInterface;
use Imagine\Image\Fill\Gradient\Horizontal;
use Imagine\Image\Fill\Gradient\Linear;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Imagine\Image\ProfileInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\PaletteInterface;

/**
 * Image implementation using the Imagick PHP extension
 */
final class Image extends AbstractImage
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
     * @var PaletteInterface
     */
    private $palette;

    /**
     * @var Boolean
     */
    private static $supportsColorspaceConversion;

    private static $colorspaceMapping = array(
        PaletteInterface::PALETTE_CMYK      => \Imagick::COLORSPACE_CMYK,
        PaletteInterface::PALETTE_RGB       => \Imagick::COLORSPACE_RGB,
        PaletteInterface::PALETTE_GRAYSCALE => \Imagick::COLORSPACE_GRAY,
    );

    /**
     * Constructs Image with Imagick and Imagine instances
     *
     * @param \Imagick         $imagick
     * @param PaletteInterface $palette
     */
    public function __construct(\Imagick $imagick, PaletteInterface $palette)
    {
        $this->detectColorspaceConversionSupport();
        $this->imagick = $imagick;
        if (static::$supportsColorspaceConversion) {
            $this->setColorspace($palette);
        }
        $this->palette = $palette;
        $this->layers = new Layers($this, $this->palette, $this->imagick);
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

        return new self($clone, $this->palette);
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
            $this->profile($this->palette->profile());
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
    public function rotate($angle, ColorInterface $background = null)
    {
        $color = $background ? $background : $this->palette->color('fff');

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
    public function save($path = null, array $options = array())
    {
        $path = null === $path ? $this->imagick->getImageFilename() : $path;
        if (null === $path) {
            throw new RuntimeException(
                'You can omit save path only if image has been open from a file'
            );
        }

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
     */
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
        try {
            $pixels = $this->imagick->getImageHistogram();
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Error while fetching histogram', $e->getCode(), $e
            );
        }

        $image = $this;

        return array_map(
            function(\ImagickPixel $pixel) use ($image) {
                return $image->pixelToColor($pixel);
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
            $pixel = $this->imagick->getImagePixelColor($point->getX(), $point->getY());
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Error while getting image pixel color', $e->getCode(), $e
            );
        }

        return $this->pixelToColor($pixel);
    }

    /**
     * Returns a color given a pixel, depending the Palette context
     *
     * Note : this method is public for PHP 5.3 compatibility
     *
     * @param \ImagickPixel $pixel
     *
     * @return ColorInterface
     *
     * @throws InvalidArgumentException In case a unknown color is requested
     */
    public function pixelToColor(\ImagickPixel $pixel)
    {
        static $colorMapping = array(
            ColorInterface::COLOR_RED     => \Imagick::COLOR_RED,
            ColorInterface::COLOR_GREEN   => \Imagick::COLOR_GREEN,
            ColorInterface::COLOR_BLUE    => \Imagick::COLOR_BLUE,
            ColorInterface::COLOR_CYAN    => \Imagick::COLOR_CYAN,
            ColorInterface::COLOR_MAGENTA => \Imagick::COLOR_MAGENTA,
            ColorInterface::COLOR_YELLOW  => \Imagick::COLOR_YELLOW,
            ColorInterface::COLOR_KEYLINE => \Imagick::COLOR_BLACK,
        );

        $alpha = $this->palette->supportsAlpha() ? (int) round($pixel->getColorValue(\Imagick::COLOR_ALPHA) * 100) : null;

        return $this->palette->color(
            array_map(function ($color) use ($pixel, $colorMapping) {
                if (!isset($colorMapping[$color])) {
                    throw new InvalidArgumentException(
                        'Color %s is not mapped in Imagick'
                    );
                }

                return $pixel->getColorValue($colorMapping[$color]) * 255;
            }, $this->palette->pixelDefinition()),
            $alpha
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
     * {@inheritdoc}
     */
    public function usePalette(PaletteInterface $palette)
    {
        if (!isset(static::$colorspaceMapping[$palette->name()])) {
            throw new InvalidArgumentException(sprintf(
                'The palette %s is not supported by Imagick driver',
                $palette->name()
            ));
        }

        if ($this->palette->name() === $palette->name()) {
            return $this;
        }

        if (!static::$supportsColorspaceConversion) {
            throw new RuntimeException('Your version of Imagick does not support colorspace conversions.');
        }

        try {
            try {
                $hasICCProfile = (Boolean) $this->imagick->getImageProfile('icc');
            } catch (\ImagickException $e) {
                $hasICCProfile = false;
            }

            if (!$hasICCProfile) {
                $this->profile($this->palette->profile());
            }

            $this->profile($palette->profile());
            $this->setColorspace($palette);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to set colorspace', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function palette()
    {
        return $this->palette;
    }

    /**
     * {@inheritdoc}
     */
    public function profile(ProfileInterface $profile)
    {
        try {
            $this->imagick->profileImage('icc', $profile->data());
        } catch (\ImagickException $e) {
            throw new RuntimeException(sprintf(
                'Unable to add profile %s to image', $profile->name()
            ), $e->getCode(), $e);
        }

        return $this;
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
     * @param ColorInterface $color
     *
     * @return string
     */
    private function getColor(ColorInterface $color)
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

    /**
     * Sets colorspace and image type, assigns the palette.
     *
     * @param PaletteInterface $palette
     *
     * @throws InvalidArgumentException
     */
    private function setColorspace(PaletteInterface $palette)
    {
        static $typeMapping = array(
            // We use Matte variants to preserve alpha
            PaletteInterface::PALETTE_CMYK      => \Imagick::IMGTYPE_TRUECOLORMATTE,
            PaletteInterface::PALETTE_RGB       => \Imagick::IMGTYPE_TRUECOLORMATTE,
            PaletteInterface::PALETTE_GRAYSCALE => \Imagick::IMGTYPE_GRAYSCALEMATTE,
        );

        if (!isset(static::$colorspaceMapping[$palette->name()])) {
            throw new InvalidArgumentException(sprintf(
                'The palette %s is not supported by Imagick driver',
                $palette->name()
            ));
        }

        $this->imagick->setType($typeMapping[$palette->name()]);
        $this->imagick->setColorspace(static::$colorspaceMapping[$palette->name()]);
        $this->palette = $palette;
    }

    /**
     * Older imagemagick versions does not support colorspace conversions.
     * Let's detect if it is supported.
     *
     * @return Boolean
     */
    private function detectColorspaceConversionSupport()
    {
        if (null !== static::$supportsColorspaceConversion) {
            return static::$supportsColorspaceConversion;
        }

        return static::$supportsColorspaceConversion = method_exists('Imagick', 'setColorspace');
    }
}
