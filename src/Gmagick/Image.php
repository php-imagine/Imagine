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

use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\NotSupportedException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;
use Imagine\Factory\ClassFactoryInterface;
use Imagine\Image\AbstractImage;
use Imagine\Image\BoxInterface;
use Imagine\Image\Fill\FillInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Metadata\MetadataBag;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\PaletteInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Imagine\Image\ProfileInterface;

/**
 * Image implementation using the Gmagick PHP extension.
 */
final class Image extends AbstractImage
{
    /**
     * @var \Gmagick
     */
    private $gmagick;

    /**
     * @var \Imagine\Gmagick\Layers|null
     */
    private $layers;

    /**
     * @var \Imagine\Image\Palette\PaletteInterface
     */
    private $palette;

    /**
     * @var array|null
     */
    private static $colorspaceMapping = null;

    /**
     * Constructs a new Image instance.
     *
     * @param \Gmagick $gmagick
     * @param \Imagine\Image\Palette\PaletteInterface $palette
     * @param \Imagine\Image\Metadata\MetadataBag $metadata
     */
    public function __construct(\Gmagick $gmagick, PaletteInterface $palette, MetadataBag $metadata)
    {
        $this->metadata = $metadata;
        $this->gmagick = $gmagick;
        $this->setColorspace($palette);
    }

    /**
     * Destroys allocated gmagick resources.
     */
    public function __destruct()
    {
        if ($this->gmagick instanceof \Gmagick) {
            $this->gmagick->clear();
            $this->gmagick->destroy();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\AbstractImage::__clone()
     */
    public function __clone()
    {
        parent::__clone();
        $this->gmagick = clone $this->gmagick;
        $this->palette = clone $this->palette;
        if ($this->layers !== null) {
            $this->layers = $this->getClassFactory()->createLayers(ClassFactoryInterface::HANDLE_GMAGICK, $this, $this->layers->key());
        }
    }

    /**
     * Returns gmagick instance.
     *
     * @return \Gmagick
     */
    public function getGmagick()
    {
        return $this->gmagick;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::copy()
     */
    public function copy()
    {
        return clone $this;
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
            $this->gmagick->cropimage($size->getWidth(), $size->getHeight(), $start->getX(), $start->getY());
        } catch (\GmagickException $e) {
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
            $this->gmagick->flopimage();
        } catch (\GmagickException $e) {
            throw new RuntimeException('Horizontal flip operation failed', $e->getCode(), $e);
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
            $this->gmagick->flipimage();
        } catch (\GmagickException $e) {
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
            $this->gmagick->stripimage();
        } catch (\GmagickException $e) {
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
            throw new InvalidArgumentException(sprintf('Gmagick\Image can only paste() Gmagick\Image instances, %s given', get_class($image)));
        }

        $alpha = (int) round($alpha);
        if ($alpha < 0 || $alpha > 100) {
            throw new InvalidArgumentException(sprintf('The %1$s argument can range from %2$d to %3$d, but you specified %4$d.', '$alpha', 0, 100, $alpha));
        }

        if ($alpha === 100) {
            try {
                $this->gmagick->compositeimage($image->gmagick, \Gmagick::COMPOSITE_DEFAULT, $start->getX(), $start->getY());
            } catch (\GmagickException $e) {
                throw new RuntimeException('Paste operation failed', $e->getCode(), $e);
            }
        } elseif ($alpha > 0) {
            throw new NotSupportedException('Gmagick doesn\'t support paste with alpha.', 1);
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
        static $supportedFilters = array(
            ImageInterface::FILTER_UNDEFINED => \Gmagick::FILTER_UNDEFINED,
            ImageInterface::FILTER_BESSEL => \Gmagick::FILTER_BESSEL,
            ImageInterface::FILTER_BLACKMAN => \Gmagick::FILTER_BLACKMAN,
            ImageInterface::FILTER_BOX => \Gmagick::FILTER_BOX,
            ImageInterface::FILTER_CATROM => \Gmagick::FILTER_CATROM,
            ImageInterface::FILTER_CUBIC => \Gmagick::FILTER_CUBIC,
            ImageInterface::FILTER_GAUSSIAN => \Gmagick::FILTER_GAUSSIAN,
            ImageInterface::FILTER_HANNING => \Gmagick::FILTER_HANNING,
            ImageInterface::FILTER_HAMMING => \Gmagick::FILTER_HAMMING,
            ImageInterface::FILTER_HERMITE => \Gmagick::FILTER_HERMITE,
            ImageInterface::FILTER_LANCZOS => \Gmagick::FILTER_LANCZOS,
            ImageInterface::FILTER_MITCHELL => \Gmagick::FILTER_MITCHELL,
            ImageInterface::FILTER_POINT => \Gmagick::FILTER_POINT,
            ImageInterface::FILTER_QUADRATIC => \Gmagick::FILTER_QUADRATIC,
            ImageInterface::FILTER_SINC => \Gmagick::FILTER_SINC,
            ImageInterface::FILTER_TRIANGLE => \Gmagick::FILTER_TRIANGLE,
        );

        if (!array_key_exists($filter, $supportedFilters)) {
            throw new InvalidArgumentException('Unsupported filter type');
        }

        try {
            $this->gmagick->resizeimage($size->getWidth(), $size->getHeight(), $supportedFilters[$filter], 1);
        } catch (\GmagickException $e) {
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
        try {
            if ($background === null) {
                $background = $this->palette->color('fff');
            }
            $pixel = $this->getColor($background);

            $this->gmagick->rotateimage($pixel, $angle);

            unset($pixel);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Rotate operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * Applies options before save or output.
     *
     * @param \Gmagick $image
     * @param array $options
     * @param string $path
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    private function applyImageOptions(\Gmagick $image, array $options, $path)
    {
        if (isset($options['format'])) {
            $format = $options['format'];
        } elseif ('' !== $extension = pathinfo($path, \PATHINFO_EXTENSION)) {
            $format = $extension;
        } else {
            $format = pathinfo($image->getImageFilename(), \PATHINFO_EXTENSION);
        }

        $format = strtolower($format);

        switch ($format) {
            case 'jpeg':
            case 'jpg':
            case 'pjpeg':
                if (!isset($options['jpeg_quality'])) {
                    if (isset($options['quality'])) {
                        $options['jpeg_quality'] = $options['quality'];
                    }
                }
                if (isset($options['jpeg_quality'])) {
                    $image->setCompressionQuality($options['jpeg_quality']);
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
            case 'png':
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
                    $image->setCompressionQuality($compression);
                }
                break;
            case 'webp':
                if (!isset($options['webp_quality'])) {
                    if (isset($options['quality'])) {
                        $options['webp_quality'] = $options['quality'];
                    }
                }
                if (isset($options['webp_quality'])) {
                    $image->setCompressionQuality($options['webp_quality']);
                }
                break;
        }
        if (isset($options['resolution-units']) && isset($options['resolution-x']) && isset($options['resolution-y'])) {
            switch ($options['resolution-units']) {
                case ImageInterface::RESOLUTION_PIXELSPERCENTIMETER:
                    $image->setimageunits(\Gmagick::RESOLUTION_PIXELSPERCENTIMETER);
                    break;
                case ImageInterface::RESOLUTION_PIXELSPERINCH:
                    $image->setimageunits(\Gmagick::RESOLUTION_PIXELSPERINCH);
                    break;
                default:
                    throw new InvalidArgumentException('Unsupported image unit format');
            }
            $image->setimageresolution($options['resolution-x'], $options['resolution-y']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::save()
     */
    public function save($path = null, array $options = array())
    {
        $path = null === $path ? $this->gmagick->getImageFilename() : $path;

        if ('' === trim($path)) {
            throw new RuntimeException('You can omit save path only if image has been open from a file');
        }

        try {
            $this->prepareOutput($options, $path);
            $allFrames = !isset($options['animated']) || false === $options['animated'];
            $this->gmagick->writeimage($path, $allFrames);
        } catch (\GmagickException $e) {
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
        header('Content-type: ' . $this->getMimeType($format));
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
        } catch (\GmagickException $e) {
            throw new RuntimeException('Get operation failed', $e->getCode(), $e);
        }

        return $this->gmagick->getimagesblob();
    }

    /**
     * @param array $options
     * @param string $path
     */
    private function prepareOutput(array $options, $path = null)
    {
        if (isset($options['animated']) && true === $options['animated']) {
            $format = isset($options['format']) ? $options['format'] : 'gif';
            $delay = isset($options['animated.delay']) ? $options['animated.delay'] : null;
            $loops = isset($options['animated.loops']) ? $options['animated.loops'] : 0;

            $options['flatten'] = false;

            $this->layers()->animate($format, $delay, $loops);
        } else {
            $this->layers()->merge();
        }
        $this->applyImageOptions($this->gmagick, $options, $path);

        // flatten only if image has multiple layers
        if ((!isset($options['flatten']) || $options['flatten'] === true) && $this->layers()->count() > 1) {
            $this->flatten();
        }
        if (isset($options['format'])) {
            $this->gmagick->setimageformat(strtoupper($options['format']));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::__toString()
     */
    public function __toString()
    {
        return $this->get('png');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::draw()
     */
    public function draw()
    {
        return $this->getClassFactory()->createDrawer(ClassFactoryInterface::HANDLE_GMAGICK, $this->gmagick);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::effects()
     */
    public function effects()
    {
        return $this->getClassFactory()->createEffects(ClassFactoryInterface::HANDLE_GMAGICK, $this->gmagick);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::getSize()
     */
    public function getSize()
    {
        try {
            $i = $this->gmagick->getimageindex();
            $this->gmagick->setimageindex(0); //rewind
            $width = $this->gmagick->getimagewidth();
            $height = $this->gmagick->getimageheight();
            $this->gmagick->setimageindex($i);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Get size operation failed', $e->getCode(), $e);
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
            throw new InvalidArgumentException('Can only apply instances of Imagine\Gmagick\Image as masks');
        }

        $size = $this->getSize();
        $maskSize = $mask->getSize();

        if ($size != $maskSize) {
            throw new InvalidArgumentException(sprintf('The given mask doesn\'t match current image\'s size, current mask\'s dimensions are %s, while image\'s dimensions are %s', $maskSize, $size));
        }

        try {
            $mask = $mask->copy();
            $this->gmagick->compositeimage($mask->gmagick, \Gmagick::COMPOSITE_DEFAULT, 0, 0);
        } catch (\GmagickException $e) {
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
            $mask->gmagick->modulateimage(100, 0, 100);
        } catch (\GmagickException $e) {
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
            $draw = new \GmagickDraw();
            $size = $this->getSize();

            $w = $size->getWidth();
            $h = $size->getHeight();

            for ($x = 0; $x < $w; $x++) {
                for ($y = 0; $y < $h; $y++) {
                    $pixel = $this->getColor($fill->getColor(new Point($x, $y)));

                    $draw->setfillcolor($pixel);
                    $draw->point($x, $y);

                    $pixel = null;
                }
            }

            $this->gmagick->drawimage($draw);

            $draw = null;
        } catch (\GmagickException $e) {
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
            $pixels = $this->gmagick->getimagehistogram();
        } catch (\GmagickException $e) {
            throw new RuntimeException('Error while fetching histogram', $e->getCode(), $e);
        }

        $image = $this;

        return array_map(function (\GmagickPixel $pixel) use ($image) {
            return $image->pixelToColor($pixel);
        }, $pixels);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::getColorAt()
     */
    public function getColorAt(PointInterface $point)
    {
        if (!$point->in($this->getSize())) {
            throw new InvalidArgumentException(sprintf('Error getting color at point [%s,%s]. The point must be inside the image of size [%s,%s]', $point->getX(), $point->getY(), $this->getSize()->getWidth(), $this->getSize()->getHeight()));
        }

        try {
            $cropped = clone $this->gmagick;
            $histogram = $cropped
                ->rollimage(-$point->getX(), -$point->getY())
                ->cropImage(1, 1, 0, 0)
                ->getImageHistogram();
        } catch (\GmagickException $e) {
            throw new RuntimeException('Unable to get the pixel', $e->getCode(), $e);
        }

        $pixel = array_shift($histogram);

        unset($histogram, $cropped);

        return $this->pixelToColor($pixel);
    }

    /**
     * Returns a color given a pixel, depending the Palette context.
     *
     * Note : this method is public for PHP 5.3 compatibility
     *
     * @param \GmagickPixel $pixel
     *
     * @throws \Imagine\Exception\InvalidArgumentException In case a unknown color is requested
     *
     * @return \Imagine\Image\Palette\Color\ColorInterface
     */
    public function pixelToColor(\GmagickPixel $pixel)
    {
        static $colorMapping = array(
            ColorInterface::COLOR_RED => \Gmagick::COLOR_RED,
            ColorInterface::COLOR_GREEN => \Gmagick::COLOR_GREEN,
            ColorInterface::COLOR_BLUE => \Gmagick::COLOR_BLUE,
            ColorInterface::COLOR_CYAN => \Gmagick::COLOR_CYAN,
            ColorInterface::COLOR_MAGENTA => \Gmagick::COLOR_MAGENTA,
            ColorInterface::COLOR_YELLOW => \Gmagick::COLOR_YELLOW,
            ColorInterface::COLOR_KEYLINE => \Gmagick::COLOR_BLACK,
            // There is no gray component in \Gmagick, let's use one of the RGB comp
            ColorInterface::COLOR_GRAY => \Gmagick::COLOR_RED,
        );

        $alpha = null;
        if ($this->palette->supportsAlpha()) {
            if ($alpha === null && defined('Gmagick::COLOR_ALPHA')) {
                try {
                    $alpha = (int) round($pixel->getcolorvalue(\Gmagick::COLOR_ALPHA) * 100);
                } catch (\GmagickPixelException $e) {
                }
            }
            if ($alpha === null && defined('Gmagick::COLOR_OPACITY')) {
                try {
                    $alpha = (int) round(100 - $pixel->getcolorvalue(\Gmagick::COLOR_OPACITY) * 100);
                } catch (\GmagickPixelException $e) {
                }
            }
        }

        $multiplier = $this->palette()->getChannelsMaxValue();

        return $this->palette->color(array_map(function ($color) use ($multiplier, $pixel, $colorMapping) {
            if (!isset($colorMapping[$color])) {
                throw new InvalidArgumentException(sprintf('Color %s is not mapped in Gmagick', $color));
            }

            return $pixel->getcolorvalue($colorMapping[$color]) * $multiplier;
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
            $this->layers = $this->getClassFactory()->createLayers(ClassFactoryInterface::HANDLE_GMAGICK, $this);
        }

        return $this->layers;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::interlace()
     */
    public function interlace($scheme)
    {
        static $supportedInterlaceSchemes = array(
            ImageInterface::INTERLACE_NONE => \Gmagick::INTERLACE_NO,
            ImageInterface::INTERLACE_LINE => \Gmagick::INTERLACE_LINE,
            ImageInterface::INTERLACE_PLANE => \Gmagick::INTERLACE_PLANE,
            ImageInterface::INTERLACE_PARTITION => \Gmagick::INTERLACE_PARTITION,
        );

        if (!array_key_exists($scheme, $supportedInterlaceSchemes)) {
            throw new InvalidArgumentException('Unsupported interlace type');
        }

        $this->gmagick->setInterlaceScheme($supportedInterlaceSchemes[$scheme]);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::usePalette()
     */
    public function usePalette(PaletteInterface $palette)
    {
        $colorspaceMapping = self::getColorspaceMapping();
        if (!isset($colorspaceMapping[$palette->name()])) {
            throw new InvalidArgumentException(sprintf('The palette %s is not supported by Gmagick driver', $palette->name()));
        }

        if ($this->palette->name() === $palette->name()) {
            return $this;
        }

        try {
            try {
                $hasICCProfile = (bool) $this->gmagick->getimageprofile('ICM');
            } catch (\GmagickException $e) {
                $hasICCProfile = false;
            }

            if (!$hasICCProfile) {
                $this->profile($this->palette->profile());
            }

            $this->profile($palette->profile());

            $this->setColorspace($palette);
        } catch (\GmagickException $e) {
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
        try {
            $this->gmagick->profileimage('ICM', $profile->data());
        } catch (\GmagickException $e) {
            if (false !== strpos($e->getMessage(), 'LCMS encoding not enabled')) {
                throw new RuntimeException(sprintf('Unable to add profile %s to image, be sue to compile graphicsmagick with `--with-lcms2` option', $profile->name()), $e->getCode(), $e);
            }

            throw new RuntimeException(sprintf('Unable to add profile %s to image', $profile->name()), $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * Flatten the image.
     */
    private function flatten()
    {
        /*
         * @see http://pecl.php.net/bugs/bug.php?id=22435
         */
        if (method_exists($this->gmagick, 'flattenImages')) {
            try {
                $this->gmagick = $this->gmagick->flattenImages();
            } catch (\GmagickException $e) {
                throw new RuntimeException('Flatten operation failed', $e->getCode(), $e);
            }
        }
    }

    /**
     * Gets specifically formatted color string from Color instance.
     *
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return \GmagickPixel
     */
    private function getColor(ColorInterface $color)
    {
        if (!$color->isOpaque()) {
            throw new InvalidArgumentException('Gmagick doesn\'t support transparency');
        }

        return new \GmagickPixel((string) $color);
    }

    /**
     * Get the mime type based on format.
     *
     * @param string $format
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return string mime-type
     */
    private function getMimeType($format)
    {
        static $mimeTypes = array(
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'wbmp' => 'image/vnd.wap.wbmp',
            'xbm' => 'image/xbm',
            'bmp' => 'image/bmp',
        );

        if (!isset($mimeTypes[$format])) {
            throw new InvalidArgumentException(sprintf('Unsupported format given. Only %s are supported, %s given', implode(', ', array_keys($mimeTypes)), $format));
        }

        return $mimeTypes[$format];
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
        $colorspaceMapping = self::getColorspaceMapping();
        if (!isset($colorspaceMapping[$palette->name()])) {
            throw new InvalidArgumentException(sprintf('The palette %s is not supported by Gmagick driver', $palette->name()));
        }

        $this->gmagick->setimagecolorspace($colorspaceMapping[$palette->name()]);
        $this->palette = $palette;
    }

    /**
     * @return array
     */
    private static function getColorspaceMapping()
    {
        if (self::$colorspaceMapping === null) {
            $csm = array(
                PaletteInterface::PALETTE_CMYK => \Gmagick::COLORSPACE_CMYK,
                PaletteInterface::PALETTE_RGB => \Gmagick::COLORSPACE_RGB,
            );
            if (defined('Gmagick::COLORSPACE_GRAY')) {
                $csm[PaletteInterface::PALETTE_GRAYSCALE] = \Gmagick::COLORSPACE_GRAY;
            }
            self::$colorspaceMapping = $csm;
        }

        return self::$colorspaceMapping;
    }
}
