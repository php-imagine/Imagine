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

use Imagine\Driver\InfoProvider;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;
use Imagine\Factory\ClassFactoryInterface;
use Imagine\Image\AbstractImage;
use Imagine\Image\BoxInterface;
use Imagine\Image\Fill\FillInterface;
use Imagine\Image\Fill\Gradient\Horizontal;
use Imagine\Image\Fill\Gradient\Linear;
use Imagine\Image\Format;
use Imagine\Image\ImageInterface;
use Imagine\Image\Metadata\MetadataBag;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\PaletteInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Imagine\Image\ProfileInterface;
use Imagine\Utils\ErrorHandling;

/**
 * Image implementation using the Imagick PHP extension.
 */
final class Image extends AbstractImage implements InfoProvider
{
    /**
     * @var \Imagick
     */
    private $imagick;

    /**
     * @var \Imagine\Imagick\Layers|null
     */
    private $layers;

    /**
     * @var \Imagine\Image\Palette\PaletteInterface
     */
    private $palette;

    /**
     * @var array
     */
    private static $colorspaceMapping = array(
        PaletteInterface::PALETTE_CMYK => \Imagick::COLORSPACE_CMYK,
        PaletteInterface::PALETTE_RGB => \Imagick::COLORSPACE_RGB,
        PaletteInterface::PALETTE_GRAYSCALE => \Imagick::COLORSPACE_GRAY,
    );

    /**
     * Constructs a new Image instance.
     *
     * @param \Imagick $imagick
     * @param \Imagine\Image\Palette\PaletteInterface $palette
     * @param \Imagine\Image\Metadata\MetadataBag $metadata
     */
    public function __construct(\Imagick $imagick, PaletteInterface $palette, MetadataBag $metadata)
    {
        $this->metadata = $metadata;
        $this->imagick = $imagick;
        if (static::getDriverInfo()->hasFeature(DriverInfo::FEATURE_COLORSPACECONVERSION)) {
            $this->setColorspace($palette);
        }
        $this->palette = $palette;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\AbstractImage::__clone()
     */
    public function __clone()
    {
        parent::__clone();
        if ($this->imagick instanceof \Imagick) {
            $this->imagick = $this->cloneImagick();
        }
        $this->palette = clone $this->palette;
        if ($this->layers !== null) {
            $this->layers = $this->getClassFactory()->createLayers(ClassFactoryInterface::HANDLE_IMAGICK, $this, $this->layers->key());
        }
    }

    /**
     * Destroys allocated imagick resources.
     */
    public function __destruct()
    {
        if ($this->imagick instanceof \Imagick) {
            $this->imagick->clear();
            $this->imagick->destroy();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\InfoProvider::getDriverInfo()
     * @since 1.3.0
     */
    public static function getDriverInfo($required = true)
    {
        return DriverInfo::get($required);
    }

    /**
     * Returns the underlying \Imagick instance.
     *
     * @return \Imagick
     */
    public function getImagick()
    {
        return $this->imagick;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::copy()
     */
    public function copy()
    {
        try {
            return clone $this;
        } catch (\ImagickException $e) {
            throw new RuntimeException('Copy operation failed', $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::crop()
     */
    public function crop(PointInterface $start, BoxInterface $size)
    {
        if (!$start->in($this->getSize())) {
            throw new OutOfBoundsException('Crop coordinates must start at minimum 0, 0 position from top left corner, crop height and width must be positive integers and must not exceed the current image borders');
        }
        try {
            if ($this->layers()->count() > 1) {
                // Crop each layer separately
                $this->imagick = $this->imagick->coalesceImages();
                foreach ($this->imagick as $frame) {
                    $frame->cropImage($size->getWidth(), $size->getHeight(), $start->getX(), $start->getY());
                    // Reset canvas for gif format
                    $frame->setImagePage(0, 0, 0, 0);
                }
                $this->imagick = $this->imagick->deconstructImages();
            } else {
                $this->imagick->cropImage($size->getWidth(), $size->getHeight(), $start->getX(), $start->getY());
                // Reset canvas for gif format
                $this->imagick->setImagePage(0, 0, 0, 0);
            }
        } catch (\ImagickException $e) {
            throw new RuntimeException('Crop operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::flipHorizontally()
     */
    public function flipHorizontally()
    {
        try {
            $this->imagick->flopImage();
        } catch (\ImagickException $e) {
            throw new RuntimeException('Horizontal Flip operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::flipVertically()
     */
    public function flipVertically()
    {
        try {
            $this->imagick->flipImage();
        } catch (\ImagickException $e) {
            throw new RuntimeException('Vertical flip operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::strip()
     */
    public function strip()
    {
        try {
            try {
                $this->profile($this->palette->profile());
            } catch (\Exception $e) {
                // here we discard setting the profile as the previous incorporated profile
                // is corrupted, let's now strip the image
            }
            $this->imagick->stripImage();
        } catch (\ImagickException $e) {
            throw new RuntimeException('Strip operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::paste()
     */
    public function paste(ImageInterface $image, PointInterface $start, $alpha = 100)
    {
        if (!$image instanceof self) {
            throw new InvalidArgumentException(sprintf('Imagick\Image can only paste() Imagick\Image instances, %s given', get_class($image)));
        }

        $alpha = (int) round($alpha);
        if ($alpha < 0 || $alpha > 100) {
            throw new InvalidArgumentException(sprintf('The %1$s argument can range from %2$d to %3$d, but you specified %4$d.', '$alpha', 0, 100, $alpha));
        }

        if ($alpha === 100) {
            $pasteMe = $image->imagick;
        } elseif ($alpha > 0) {
            $pasteMe = $image->cloneImagick();
            // setImageOpacity was replaced with setImageAlpha in php-imagick v3.4.3
            if (method_exists($pasteMe, 'setImageAlpha')) {
                $pasteMe->setImageAlpha($alpha / 100);
            } else {
                ErrorHandling::ignoring(E_DEPRECATED, function () use ($pasteMe, $alpha) {
                    $pasteMe->setImageOpacity($alpha / 100);
                });
            }
        } else {
            $pasteMe = null;
        }
        if ($pasteMe !== null) {
            try {
                $this->imagick->compositeImage($pasteMe, \Imagick::COMPOSITE_DEFAULT, $start->getX(), $start->getY());
                $error = null;
            } catch (\ImagickException $e) {
                $error = $e;
            }
            if ($pasteMe !== $image->imagick) {
                $pasteMe->clear();
                $pasteMe->destroy();
            }
            if ($error !== null) {
                throw new RuntimeException('Paste operation failed', $error->getCode(), $error);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::resize()
     */
    public function resize(BoxInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        try {
            if ($this->layers()->count() > 1) {
                $this->imagick = $this->imagick->coalesceImages();
                foreach ($this->imagick as $frame) {
                    $frame->resizeImage($size->getWidth(), $size->getHeight(), $this->getFilter($filter), 1);
                }
                $this->imagick = $this->imagick->deconstructImages();
            } else {
                $this->imagick->resizeImage($size->getWidth(), $size->getHeight(), $this->getFilter($filter), 1);
            }
        } catch (\ImagickException $e) {
            throw new RuntimeException('Resize operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::rotate()
     */
    public function rotate($angle, ColorInterface $background = null)
    {
        if ($background === null) {
            $background = $this->palette->color('fff');
        }

        try {
            $pixel = $this->getColor($background);

            $this->imagick->rotateimage($pixel, $angle);
            $this->imagick->setImagePage(0, 0, 0, 0);

            $pixel->clear();
            $pixel->destroy();
        } catch (\ImagickException $e) {
            throw new RuntimeException('Rotate operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::save()
     */
    public function save($path = null, array $options = array())
    {
        $path = $path === null ? $this->imagick->getImageFilename() : $path;
        if ($path === null) {
            throw new RuntimeException('You can omit save path only if image has been open from a file');
        }

        try {
            $this->prepareOutput($options, $path);
            $this->imagick->writeImages($path, true);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Save operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::show()
     */
    public function show($format, array $options = array())
    {
        $formatInfo = static::getDriverInfo()->getSupportedFormats()->find($format);
        if ($formatInfo === null) {
            throw new InvalidArgumentException(sprintf(
                'Displaying an image in "%s" format is not supported, please use one of the following formats: "%s"',
                $format,
                implode('", "', static::getDriverInfo()->getSupportedFormats()->getAllIDs())
            ));
        }
        header('Content-type: ' . $formatInfo->getMimeType());
        echo $this->get($format, $options);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::get()
     */
    public function get($format, array $options = array())
    {
        try {
            $options['format'] = $format;
            $this->prepareOutput($options);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Get operation failed', $e->getCode(), $e);
        }

        return $this->imagick->getImagesBlob();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::interlace()
     */
    public function interlace($scheme)
    {
        static $supportedInterlaceSchemes = array(
            ImageInterface::INTERLACE_NONE => \Imagick::INTERLACE_NO,
            ImageInterface::INTERLACE_LINE => \Imagick::INTERLACE_LINE,
            ImageInterface::INTERLACE_PLANE => \Imagick::INTERLACE_PLANE,
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
     * @param string $path
     */
    private function prepareOutput(array $options, $path = null)
    {
        if (isset($options['animated']) && $options['animated'] === true) {
            $format = isset($options['format']) ? $options['format'] : Format::ID_GIF;
            $delay = isset($options['animated.delay']) ? $options['animated.delay'] : null;
            $loops = isset($options['animated.loops']) ? $options['animated.loops'] : 0;

            $options['flatten'] = false;

            $this->layers()->animate($format, $delay, $loops);
        } else {
            $this->layers()->merge();
        }
        $this->imagick = $this->applyImageOptions($this->imagick, $options, $path);

        // flatten only if image has multiple layers
        if ((!isset($options['flatten']) || $options['flatten'] === true) && $this->layers()->count() > 1) {
            $this->flatten();
        }

        if (isset($options['format'])) {
            $this->imagick->setImageFormat($options['format']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::__toString()
     */
    public function __toString()
    {
        return $this->get(Format::ID_PNG);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::draw()
     */
    public function draw()
    {
        return $this->getClassFactory()->createDrawer(ClassFactoryInterface::HANDLE_IMAGICK, $this->imagick);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::effects()
     */
    public function effects()
    {
        return $this->getClassFactory()->createEffects(ClassFactoryInterface::HANDLE_IMAGICK, $this->imagick);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::getSize()
     */
    public function getSize()
    {
        try {
            $i = $this->imagick->getIteratorIndex();
            $this->imagick->rewind();
            $width = $this->imagick->getImageWidth();
            $height = $this->imagick->getImageHeight();
            $this->imagick->setIteratorIndex($i);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Could not get size', $e->getCode(), $e);
        }

        return $this->getClassFactory()->createBox($width, $height);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::applyMask()
     */
    public function applyMask(ImageInterface $mask)
    {
        if (!$mask instanceof self) {
            throw new InvalidArgumentException('Can only apply instances of Imagine\Imagick\Image as masks');
        }

        $size = $this->getSize();
        $maskSize = $mask->getSize();

        if ($size != $maskSize) {
            throw new InvalidArgumentException(sprintf('The given mask doesn\'t match current image\'s size, Current mask\'s dimensions are %s, while image\'s dimensions are %s', $maskSize, $size));
        }

        $mask = $mask->mask();
        $mask->imagick->negateImage(true);

        try {
            // remove transparent areas of the original from the mask
            $mask->imagick->compositeImage($this->imagick, \Imagick::COMPOSITE_DSTIN, 0, 0);
            $this->imagick->compositeImage($mask->imagick, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);

            $mask->imagick->clear();
            $mask->imagick->destroy();
        } catch (\ImagickException $e) {
            throw new RuntimeException('Apply mask operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::mask()
     */
    public function mask()
    {
        $mask = $this->copy();

        try {
            $mask->imagick->modulateImage(100, 0, 100);
            $mask->imagick->setImageMatte(false);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Mask operation failed', $e->getCode(), $e);
        }

        return $mask;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::fill()
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
                        $pixel->setColorValue(\Imagick::COLOR_ALPHA, $color->getAlpha() / 100);
                    }

                    $iterator->syncIterator();
                }
            }
        } catch (\ImagickException $e) {
            throw new RuntimeException('Fill operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::histogram()
     */
    public function histogram()
    {
        try {
            $pixels = $this->imagick->getImageHistogram();
        } catch (\ImagickException $e) {
            throw new RuntimeException('Error while fetching histogram', $e->getCode(), $e);
        }

        $image = $this;

        return array_values(array_unique(array_map(function (\ImagickPixel $pixel) use ($image) {
            return $image->pixelToColor($pixel);
        }, $pixels)));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::getColorAt()
     */
    public function getColorAt(PointInterface $point)
    {
        if (!$point->in($this->getSize())) {
            throw new RuntimeException(sprintf('Error getting color at point [%s,%s]. The point must be inside the image of size [%s,%s]', $point->getX(), $point->getY(), $this->getSize()->getWidth(), $this->getSize()->getHeight()));
        }

        try {
            $pixel = $this->imagick->getImagePixelColor($point->getX(), $point->getY());
        } catch (\ImagickException $e) {
            throw new RuntimeException('Error while getting image pixel color', $e->getCode(), $e);
        }

        return $this->pixelToColor($pixel);
    }

    /**
     * Returns a color given a pixel, depending the Palette context.
     *
     * Note : this method is public for PHP 5.3 compatibility
     *
     * @param \ImagickPixel $pixel
     *
     * @throws \Imagine\Exception\InvalidArgumentException In case a unknown color is requested
     *
     * @return \Imagine\Image\Palette\Color\ColorInterface
     */
    public function pixelToColor(\ImagickPixel $pixel)
    {
        static $colorMapping = array(
            ColorInterface::COLOR_RED => \Imagick::COLOR_RED,
            ColorInterface::COLOR_GREEN => \Imagick::COLOR_GREEN,
            ColorInterface::COLOR_BLUE => \Imagick::COLOR_BLUE,
            ColorInterface::COLOR_CYAN => \Imagick::COLOR_CYAN,
            ColorInterface::COLOR_MAGENTA => \Imagick::COLOR_MAGENTA,
            ColorInterface::COLOR_YELLOW => \Imagick::COLOR_YELLOW,
            ColorInterface::COLOR_KEYLINE => \Imagick::COLOR_BLACK,
            // There is no gray component in \Imagick, let's use one of the RGB comp
            ColorInterface::COLOR_GRAY => \Imagick::COLOR_RED,
        );

        $alpha = $this->palette->supportsAlpha() ? (int) round($pixel->getColorValue(\Imagick::COLOR_ALPHA) * 100) : null;
        if ($alpha) {
            $alpha = min(max($alpha, 0), 100);
        }

        $multiplier = $this->palette()->getChannelsMaxValue();

        return $this->palette->color(array_map(function ($color) use ($multiplier, $pixel, $colorMapping) {
            if (!isset($colorMapping[$color])) {
                throw new InvalidArgumentException(sprintf('Color %s is not mapped in Imagick', $color));
            }

            return $pixel->getColorValue($colorMapping[$color]) * $multiplier;
        }, $this->palette->pixelDefinition()), $alpha);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::layers()
     */
    public function layers()
    {
        if ($this->layers === null) {
            $this->layers = $this->getClassFactory()->createLayers(ClassFactoryInterface::HANDLE_IMAGICK, $this);
        }

        return $this->layers;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::usePalette()
     */
    public function usePalette(PaletteInterface $palette)
    {
        if ($this->palette->name() === $palette->name()) {
            return $this;
        }

        if (!isset(static::$colorspaceMapping[$palette->name()])) {
            throw new InvalidArgumentException(sprintf('The palette %s is not supported by Imagick driver', $palette->name()));
        }

        static::getDriverInfo()->requireFeature(DriverInfo::FEATURE_COLORSPACECONVERSION);

        try {
            try {
                $hasICCProfile = (bool) $this->imagick->getImageProfile('icc');
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
     *
     * @see \Imagine\Image\ImageInterface::palette()
     */
    public function palette()
    {
        return $this->palette;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::profile()
     */
    public function profile(ProfileInterface $profile)
    {
        static::getDriverInfo()->requireFeature(DriverInfo::FEATURE_COLORPROFILES);
        try {
            $this->imagick->profileImage('icc', $profile->data());
        } catch (\ImagickException $e) {
            throw new RuntimeException(sprintf('Unable to add profile %s to image', $profile->name()), $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * Flatten the image.
     */
    private function flatten()
    {
        // @see https://github.com/mkoppanen/imagick/issues/45
        try {
            if (method_exists($this->imagick, 'mergeImageLayers') && defined('Imagick::LAYERMETHOD_UNDEFINED')) {
                $this->imagick = $this->imagick->mergeImageLayers(\Imagick::LAYERMETHOD_UNDEFINED);
            } elseif (method_exists($this->imagick, 'flattenImages')) {
                $this->imagick = $this->imagick->flattenImages();
            }
        } catch (\ImagickException $e) {
            throw new RuntimeException('Flatten operation failed', $e->getCode(), $e);
        }
    }

    /**
     * Applies options before save or output.
     *
     * @param \Imagick $image
     * @param array $options
     * @param string $path
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return \Imagick
     */
    private function applyImageOptions(\Imagick $image, array $options, $path)
    {
        if (isset($options['format'])) {
            $format = $options['format'];
        } elseif ('' !== $extension = pathinfo($path, \PATHINFO_EXTENSION)) {
            $format = $extension;
        } else {
            $format = pathinfo($image->getImageFilename(), \PATHINFO_EXTENSION);
        }

        $formatInfo = Format::get($format);
        switch ($formatInfo === null ? '' : $formatInfo->getID()) {
            case Format::ID_JPEG:
                if (!isset($options['jpeg_quality'])) {
                    if (isset($options['quality'])) {
                        $options['jpeg_quality'] = $options['quality'];
                    }
                }
                if (isset($options['jpeg_quality'])) {
                    $image->setimagecompressionquality($options['jpeg_quality']);
                    $image->setcompressionquality($options['jpeg_quality']);
                }
                if (isset($options['jpeg_sampling_factors'])) {
                    if (!is_array($options['jpeg_sampling_factors']) || \count($options['jpeg_sampling_factors']) < 1) {
                        throw new InvalidArgumentException('jpeg_sampling_factors option should be an array of integers');
                    }
                    $image->setSamplingFactors(array_map(function ($factor) {
                        return (int) $factor;
                    }, $options['jpeg_sampling_factors']));
                }
                break;
            case Format::ID_PNG:
                if (!isset($options['png_compression_level'])) {
                    if (isset($options['quality'])) {
                        $options['png_compression_level'] = round((100 - $options['quality']) * 9 / 100);
                    }
                }
                if (isset($options['png_compression_level'])) {
                    if ($options['png_compression_level'] < 0 || $options['png_compression_level'] > 9) {
                        throw new InvalidArgumentException('png_compression_level option should be an integer from 0 to 9');
                    }
                }
                if (isset($options['png_compression_filter'])) {
                    if ($options['png_compression_filter'] < 0 || $options['png_compression_filter'] > 9) {
                        throw new InvalidArgumentException('png_compression_filter option should be an integer from 0 to 9');
                    }
                }
                if (isset($options['png_compression_level']) || isset($options['png_compression_filter'])) {
                    // first digit: compression level (default: 7)
                    $compression = isset($options['png_compression_level']) ? $options['png_compression_level'] * 10 : 70;
                    // second digit: compression filter (default: 5)
                    $compression += isset($options['png_compression_filter']) ? $options['png_compression_filter'] : 5;
                    $image->setimagecompressionquality($compression);
                    $image->setcompressionquality($compression);
                }
                break;
            case Format::ID_WEBP:
                if (!isset($options['webp_quality'])) {
                    if (isset($options['quality'])) {
                        $options['webp_quality'] = $options['quality'];
                    }
                }
                if (isset($options['webp_quality'])) {
                    $image->setImageCompressionQuality($options['webp_quality']);
                }
                if (isset($options['webp_lossless'])) {
                    $image->setOption('webp:lossless', $options['webp_lossless']);
                }
                break;
            case Format::ID_AVIF:
            case Format::ID_HEIC:
                if (!empty($options[$formatInfo->getID() . '_lossless'])) {
                    $image->setimagecompressionquality(100);
                    $image->setcompressionquality(100);
                } else {
                    if (!isset($options[$formatInfo->getID() . '_quality'])) {
                        if (isset($options['quality'])) {
                            $options[$formatInfo->getID() . '_quality'] = $options['quality'];
                        }
                    }
                    if (isset($options[$formatInfo->getID() . '_quality'])) {
                        $options[$formatInfo->getID() . '_quality'] = max(1, min(99, $options[$formatInfo->getID() . '_quality']));
                        $image->setimagecompressionquality($options[$formatInfo->getID() . '_quality']);
                        $image->setcompressionquality($options[$formatInfo->getID() . '_quality']);
                    }
                }
                break;
            case Format::ID_JXL:
                if (!empty($options['jxl_lossless'])) {
                    $image->setimagecompressionquality(100);
                    $image->setcompressionquality(100);
                } else {
                    if (!isset($options['jxl_quality'])) {
                        if (isset($options['quality'])) {
                            $options['jxl_quality'] = $options['quality'];
                        }
                    }
                    if (isset($options['jxl_quality'])) {
                        $options['jxl_quality'] = max(9, min(99, $options['jxl_quality']));
                        $image->setimagecompressionquality($options['jxl_quality']);
                        $image->setcompressionquality($options['jxl_quality']);
                    }
                }
                break;
        }
        if (isset($options['resolution-units']) && isset($options['resolution-x']) && isset($options['resolution-y'])) {
            if (empty($options['resampling-filter'])) {
                $filterName = ImageInterface::FILTER_UNDEFINED;
            } else {
                $filterName = $options['resampling-filter'];
            }
            $filter = $this->getFilter($filterName);
            switch ($options['resolution-units']) {
                case ImageInterface::RESOLUTION_PIXELSPERCENTIMETER:
                    $image->setImageUnits(\Imagick::RESOLUTION_PIXELSPERCENTIMETER);
                    break;
                case ImageInterface::RESOLUTION_PIXELSPERINCH:
                    $image->setImageUnits(\Imagick::RESOLUTION_PIXELSPERINCH);
                    break;
                default:
                    throw new RuntimeException('Unsupported image unit format');
            }
            $image->setImageResolution($options['resolution-x'], $options['resolution-y']);
            $image->resampleImage($options['resolution-x'], $options['resolution-y'], $filter, 0);
        }
        if (!empty($options['optimize'])) {
            try {
                $image = $image->coalesceImages();
                $optimized = $image->optimizeimagelayers();
            } catch (\ImagickException $e) {
                throw new RuntimeException('Image optimization failed', $e->getCode(), $e);
            }
            if ($optimized === false) {
                throw new RuntimeException('Image optimization failed');
            }
            if ($optimized instanceof \Imagick) {
                $image = $optimized;
            }
        }

        return $image;
    }

    /**
     * Gets specifically formatted color string from Color instance.
     *
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     *
     * @return \ImagickPixel
     */
    private function getColor(ColorInterface $color)
    {
        $pixel = new \ImagickPixel((string) $color);
        $pixel->setColorValue(\Imagick::COLOR_ALPHA, $color->getAlpha() / 100);

        return $pixel;
    }

    /**
     * Checks whether given $fill is linear and opaque.
     *
     * @param \Imagine\Image\Fill\FillInterface $fill
     *
     * @return bool
     */
    private function isLinearOpaque(FillInterface $fill)
    {
        return $fill instanceof Linear && $fill->getStart()->isOpaque() && $fill->getEnd()->isOpaque();
    }

    /**
     * Performs optimized gradient fill for non-opaque linear gradients.
     *
     * @param \Imagine\Image\Fill\Gradient\Linear $fill
     */
    private function applyFastLinear(Linear $fill)
    {
        $gradient = new \Imagick();
        $size = $this->getSize();
        $color = sprintf('gradient:%s-%s', (string) $fill->getStart(), (string) $fill->getEnd());

        if ($fill instanceof Horizontal) {
            $gradient->newPseudoImage($size->getHeight(), $size->getWidth(), $color);
            $gradient->rotateImage(new \ImagickPixel(), 90);
        } else {
            $gradient->newPseudoImage($size->getWidth(), $size->getHeight(), $color);
        }

        $this->imagick->compositeImage($gradient, \Imagick::COMPOSITE_OVER, 0, 0);
        $gradient->clear();
        $gradient->destroy();
    }

    /**
     * Sets colorspace and image type, assigns the palette.
     *
     * @param \Imagine\Image\Palette\PaletteInterface $palette
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    private function setColorspace(PaletteInterface $palette)
    {
        $typeMapping = array(
            // We use Matte variants to preserve alpha
            //
            // (the IMGTYPE_...ALPHA constants are only available since ImageMagick 7 and Imagick 3.4.3, previously they were named
            // IMGTYPE_...MATTE but in some combinations of different Imagick and ImageMagick versions none of them are avaiable at all,
            // so we found no other way to fix it as to hard code the values here)
            PaletteInterface::PALETTE_CMYK => defined('\Imagick::IMGTYPE_TRUECOLORALPHA') ? \Imagick::IMGTYPE_TRUECOLORALPHA : (defined('\Imagick::IMGTYPE_TRUECOLORMATTE') ? \Imagick::IMGTYPE_TRUECOLORMATTE : 7),
            PaletteInterface::PALETTE_RGB => defined('\Imagick::IMGTYPE_TRUECOLORALPHA') ? \Imagick::IMGTYPE_TRUECOLORALPHA : (defined('\Imagick::IMGTYPE_TRUECOLORMATTE') ? \Imagick::IMGTYPE_TRUECOLORMATTE : 7),
            PaletteInterface::PALETTE_GRAYSCALE => defined('\Imagick::IMGTYPE_GRAYSCALEALPHA') ? \Imagick::IMGTYPE_GRAYSCALEALPHA : (defined('\Imagick::IMGTYPE_GRAYSCALEMATTE') ? \Imagick::IMGTYPE_GRAYSCALEMATTE : 3),
        );

        if (!isset(static::$colorspaceMapping[$palette->name()])) {
            throw new InvalidArgumentException(sprintf('The palette %s is not supported by Imagick driver', $palette->name()));
        }

        $this->imagick->setType($typeMapping[$palette->name()]);
        $this->imagick->setColorspace(static::$colorspaceMapping[$palette->name()]);
        if ($this->imagick->getnumberimages() > 0 && defined('Imagick::ALPHACHANNEL_REMOVE') && defined('Imagick::ALPHACHANNEL_SET')) {
            $originalImageIndex = $this->imagick->getiteratorindex();
            foreach ($this->imagick as $frame) {
                if ($palette->supportsAlpha()) {
                    $frame->setimagealphachannel(\Imagick::ALPHACHANNEL_SET);
                } else {
                    $frame->setimagealphachannel(\Imagick::ALPHACHANNEL_REMOVE);
                }
            }
            $this->imagick->setiteratorindex($originalImageIndex);
        }
        $this->palette = $palette;
    }

    /**
     * Returns the filter if it's supported.
     *
     * @param string $filter
     *
     * @throws \Imagine\Exception\InvalidArgumentException if the filter is unsupported
     *
     * @return string
     */
    private function getFilter($filter = ImageInterface::FILTER_UNDEFINED)
    {
        static $supportedFilters = null;
        if ($supportedFilters === null) {
            $supportedFilters = array(
                ImageInterface::FILTER_UNDEFINED => \Imagick::FILTER_UNDEFINED,
                ImageInterface::FILTER_BESSEL => \Imagick::FILTER_BESSEL,
                ImageInterface::FILTER_BLACKMAN => \Imagick::FILTER_BLACKMAN,
                ImageInterface::FILTER_BOX => \Imagick::FILTER_BOX,
                ImageInterface::FILTER_CATROM => \Imagick::FILTER_CATROM,
                ImageInterface::FILTER_CUBIC => \Imagick::FILTER_CUBIC,
                ImageInterface::FILTER_GAUSSIAN => \Imagick::FILTER_GAUSSIAN,
                ImageInterface::FILTER_HANNING => \Imagick::FILTER_HANNING,
                ImageInterface::FILTER_HAMMING => \Imagick::FILTER_HAMMING,
                ImageInterface::FILTER_HERMITE => \Imagick::FILTER_HERMITE,
                ImageInterface::FILTER_LANCZOS => \Imagick::FILTER_LANCZOS,
                ImageInterface::FILTER_MITCHELL => \Imagick::FILTER_MITCHELL,
                ImageInterface::FILTER_POINT => \Imagick::FILTER_POINT,
                ImageInterface::FILTER_QUADRATIC => \Imagick::FILTER_QUADRATIC,
                ImageInterface::FILTER_SINC => \Imagick::FILTER_SINC,
                ImageInterface::FILTER_TRIANGLE => \Imagick::FILTER_TRIANGLE,
            );
            if (defined('Imagick::FILTER_SINCFAST')) {
                $supportedFilters[ImageInterface::FILTER_SINCFAST] = \Imagick::FILTER_SINCFAST;
            }
        }
        if (!in_array($filter, static::getAllFilterValues(), true)) {
            throw new InvalidArgumentException('Unsupported filter type');
        }
        if (!array_key_exists($filter, $supportedFilters)) {
            $filter = ImageInterface::FILTER_UNDEFINED;
        }

        return $supportedFilters[$filter];
    }

    /**
     * Clone the Imagick resource of this instance.
     *
     * @throws \ImagickException
     *
     * @return \Imagick
     */
    protected function cloneImagick()
    {
        // the clone method has been deprecated in imagick 3.1.0b1.
        // we can't use phpversion('imagick') because it may return `@PACKAGE_VERSION@`
        // so, let's check if ImagickDraw has the setResolution method, which has been introduced in the same version 3.1.0b1
        if (method_exists('ImagickDraw', 'setResolution')) {
            return clone $this->imagick;
        }

        return $this->imagick->clone();
    }
}
