<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

use Imagine\Driver\InfoProvider;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;
use Imagine\Factory\ClassFactoryInterface;
use Imagine\Image\AbstractImage;
use Imagine\Image\BoxInterface;
use Imagine\Image\Fill\FillInterface;
use Imagine\Image\Format;
use Imagine\Image\ImageInterface;
use Imagine\Image\Metadata\MetadataBag;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB as RGBColor;
use Imagine\Image\Palette\PaletteInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Imagine\Image\ProfileInterface;
use Imagine\Utils\ErrorHandling;

/**
 * Image implementation using the GD library.
 */
final class Image extends AbstractImage implements InfoProvider
{
    /**
     * @var resource|\GdImage
     */
    private $resource;

    /**
     * @var \Imagine\Gd\Layers|null
     */
    private $layers;

    /**
     * @var \Imagine\Image\Palette\PaletteInterface
     */
    private $palette;

    /**
     * Constructs a new Image instance.
     *
     * @param resource|\GdImage $resource
     * @param \Imagine\Image\Palette\PaletteInterface $palette
     * @param \Imagine\Image\Metadata\MetadataBag $metadata
     */
    public function __construct($resource, PaletteInterface $palette, MetadataBag $metadata)
    {
        $this->metadata = $metadata;
        $this->palette = $palette;
        $this->resource = $resource;
    }

    /**
     * Makes sure the current image resource is destroyed.
     */
    public function __destruct()
    {
        if ($this->resource) {
            if (is_resource($this->resource) && get_resource_type($this->resource) === 'gd' || $this->resource instanceof \GdImage) {
                imagedestroy($this->resource);
            }
            $this->resource = null;
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
        $size = $this->getSize();
        $copy = $this->createImage($size, 'copy');
        if (imagecopy($copy, $this->resource, 0, 0, 0, 0, $size->getWidth(), $size->getHeight()) === false) {
            imagedestroy($copy);
            throw new RuntimeException('Image copy operation failed');
        }
        $this->resource = $copy;
        $this->palette = clone $this->palette;
        if ($this->layers !== null) {
            $this->layers = $this->getClassFactory()->createLayers(ClassFactoryInterface::HANDLE_GD, $this, $this->layers->key());
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
     * Returns Gd resource.
     *
     * @return resource|\GdImage
     */
    public function getGdResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::copy()
     */
    final public function copy()
    {
        return clone $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::crop()
     */
    final public function crop(PointInterface $start, BoxInterface $size)
    {
        if (!$start->in($this->getSize())) {
            throw new OutOfBoundsException('Crop coordinates must start at minimum 0, 0 position from top left corner, crop height and width must be positive integers and must not exceed the current image borders');
        }

        $width = $size->getWidth();
        $height = $size->getHeight();

        $dest = $this->createImage($size, 'crop');

        if (imagecopy($dest, $this->resource, 0, 0, $start->getX(), $start->getY(), $width, $height) === false) {
            imagedestroy($dest);
            throw new RuntimeException('Image crop operation failed');
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::paste()
     */
    final public function paste(ImageInterface $image, PointInterface $start, $alpha = 100)
    {
        if (!$image instanceof self) {
            throw new InvalidArgumentException(sprintf('Gd\Image can only paste() Gd\Image instances, %s given', get_class($image)));
        }

        $alpha = (int) round($alpha);
        if ($alpha < 0 || $alpha > 100) {
            throw new InvalidArgumentException(sprintf('The %1$s argument can range from %2$d to %3$d, but you specified %4$d.', '$alpha', 0, 100, $alpha));
        }

        $size = $image->getSize();

        if ($alpha === 100) {
            imagealphablending($this->resource, true);
            imagealphablending($image->resource, true);

            $success = imagecopy($this->resource, $image->resource, $start->getX(), $start->getY(), 0, 0, $size->getWidth(), $size->getHeight());

            imagealphablending($this->resource, false);
            imagealphablending($image->resource, false);

            if ($success === false) {
                throw new RuntimeException('Image paste operation failed');
            }
        } elseif ($alpha > 0) {
            if (imagecopymerge(/*dst_im*/$this->resource, /*src_im*/$image->resource, /*dst_x*/$start->getX(), /*dst_y*/$start->getY(), /*src_x*/0, /*src_y*/0, /*src_w*/$size->getWidth(), /*src_h*/$size->getHeight(), /*pct*/$alpha) === false) {
                throw new RuntimeException('Image paste operation failed');
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * Please remark that GD doesn't support different filters, so the $filter argument is ignored.
     *
     * @see \Imagine\Image\ManipulatorInterface::resize()
     */
    final public function resize(BoxInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        if (!in_array($filter, static::getAllFilterValues(), true)) {
            throw new InvalidArgumentException('Unsupported filter type');
        }

        $width = $size->getWidth();
        $height = $size->getHeight();

        $dest = $this->createImage($size, 'resize');

        imagealphablending($this->resource, true);
        imagealphablending($dest, true);

        $success = imagecopyresampled($dest, $this->resource, 0, 0, 0, 0, $width, $height, imagesx($this->resource), imagesy($this->resource));

        imagealphablending($this->resource, false);
        imagealphablending($dest, false);

        if ($success === false) {
            imagedestroy($dest);
            throw new RuntimeException('Image resize operation failed');
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::rotate()
     */
    final public function rotate($angle, ColorInterface $background = null)
    {
        if ($background === null) {
            $background = $this->palette->color('fff');
        }
        $color = $this->getColor($background);
        $resource = imagerotate($this->resource, -1 * $angle, $color);

        if ($resource === false) {
            throw new RuntimeException('Image rotate operation failed');
        }

        imagedestroy($this->resource);
        $this->resource = $resource;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::save()
     */
    final public function save($path = null, array $options = array())
    {
        $path = $path === null ? (isset($this->metadata['filepath']) ? $this->metadata['filepath'] : $path) : $path;

        if ($path === null) {
            throw new RuntimeException('You can omit save path only if image has been open from a file');
        }

        if (isset($options['format'])) {
            $format = $options['format'];
        } elseif ('' !== $extension = pathinfo($path, \PATHINFO_EXTENSION)) {
            $format = $extension;
        } else {
            $originalPath = isset($this->metadata['filepath']) ? $this->metadata['filepath'] : null;
            $format = pathinfo($originalPath, \PATHINFO_EXTENSION);
        }
        $formatInfo = static::getDriverInfo()->getSupportedFormats()->find($format);
        if ($formatInfo === null) {
            throw new InvalidArgumentException(sprintf(
                'Saving image in "%s" format is not supported, please use one of the following extensions: "%s"',
                $format,
                implode('", "', static::getDriverInfo()->getSupportedFormats()->getAllIDs())
            ));
        }
        $this->saveOrOutput($formatInfo, $options, $path);

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
        $this->saveOrOutput($formatInfo, $options);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::get()
     */
    public function get($format, array $options = array())
    {
        $formatInfo = static::getDriverInfo()->getSupportedFormats()->find($format);
        if ($formatInfo === null) {
            throw new InvalidArgumentException(sprintf(
                'Creating an image in "%s" format is not supported, please use one of the following formats: "%s"',
                $format,
                implode('", "', static::getDriverInfo()->getSupportedFormats()->getAllIDs())
            ));
        }
        ob_start();
        $this->saveOrOutput($formatInfo, $options);

        return ob_get_clean();
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
     * @see \Imagine\Image\ManipulatorInterface::flipHorizontally()
     */
    final public function flipHorizontally()
    {
        if (function_exists('imageflip')) {
            imageflip($this->resource, IMG_FLIP_HORIZONTAL);
        } else {
            $size = $this->getSize();
            $width = $size->getWidth();
            $height = $size->getHeight();
            $dest = $this->createImage($size, 'flip');

            for ($i = 0; $i < $width; $i++) {
                if (imagecopy($dest, $this->resource, $i, 0, ($width - 1) - $i, 0, 1, $height) === false) {
                    imagedestroy($dest);
                    throw new RuntimeException('Horizontal flip operation failed');
                }
            }

            imagedestroy($this->resource);

            $this->resource = $dest;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::flipVertically()
     */
    final public function flipVertically()
    {
        if (function_exists('imageflip')) {
            imageflip($this->resource, IMG_FLIP_VERTICAL);
        } else {
            $size = $this->getSize();
            $width = $size->getWidth();
            $height = $size->getHeight();
            $dest = $this->createImage($size, 'flip');

            for ($i = 0; $i < $height; $i++) {
                if (imagecopy($dest, $this->resource, 0, $i, 0, ($height - 1) - $i, $width, 1) === false) {
                    imagedestroy($dest);
                    throw new RuntimeException('Vertical flip operation failed');
                }
            }

            imagedestroy($this->resource);

            $this->resource = $dest;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::strip()
     */
    final public function strip()
    {
        // GD strips profiles and comment, so there's nothing to do here
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::draw()
     */
    public function draw()
    {
        return $this->getClassFactory()->createDrawer(ClassFactoryInterface::HANDLE_GD, $this->resource);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::effects()
     */
    public function effects()
    {
        return $this->getClassFactory()->createEffects(ClassFactoryInterface::HANDLE_GD, $this->resource);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::getSize()
     */
    public function getSize()
    {
        return $this->getClassFactory()->createBox(imagesx($this->resource), imagesy($this->resource));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::applyMask()
     */
    public function applyMask(ImageInterface $mask)
    {
        if (!$mask instanceof self) {
            throw new InvalidArgumentException('Cannot mask non-gd images');
        }

        $size = $this->getSize();
        $maskSize = $mask->getSize();

        if ($size != $maskSize) {
            throw new InvalidArgumentException(sprintf('The given mask doesn\'t match current image\'s size, Current mask\'s dimensions are %s, while image\'s dimensions are %s', $maskSize, $size));
        }

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x++) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y++) {
                $position = new Point($x, $y);
                $color = $this->getColorAt($position);
                $maskColor = $mask->getColorAt($position);
                $delta = (int) round($color->getAlpha() * $maskColor->getRed() / 255) * -1;

                if (imagesetpixel($this->resource, $x, $y, $this->getColor($color->dissolve($delta))) === false) {
                    throw new RuntimeException('Apply mask operation failed');
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::fill()
     */
    public function fill(FillInterface $fill)
    {
        $size = $this->getSize();

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x++) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y++) {
                if (imagesetpixel($this->resource, $x, $y, $this->getColor($fill->getColor(new Point($x, $y)))) === false) {
                    throw new RuntimeException('Fill operation failed');
                }
            }
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

        if (imagefilter($mask->resource, IMG_FILTER_GRAYSCALE) === false) {
            throw new RuntimeException('Mask operation failed');
        }

        return $mask;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::histogram()
     */
    public function histogram()
    {
        $size = $this->getSize();
        $colors = array();

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x++) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y++) {
                $colors[] = $this->getColorAt(new Point($x, $y));
            }
        }

        return array_values(array_unique($colors));
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

        $index = imagecolorat($this->resource, $point->getX(), $point->getY());
        $info = imagecolorsforindex($this->resource, $index);

        return $this->palette->color(array($info['red'], $info['green'], $info['blue']), max(min(100 - (int) round($info['alpha'] / 127 * 100), 100), 0));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::layers()
     */
    public function layers()
    {
        if ($this->layers === null) {
            $this->layers = $this->getClassFactory()->createLayers(ClassFactoryInterface::HANDLE_GD, $this);
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
            ImageInterface::INTERLACE_NONE => 0,
            ImageInterface::INTERLACE_LINE => 1,
            ImageInterface::INTERLACE_PLANE => 1,
            ImageInterface::INTERLACE_PARTITION => 1,
        );

        if (!array_key_exists($scheme, $supportedInterlaceSchemes)) {
            throw new InvalidArgumentException('Unsupported interlace type');
        }

        imageinterlace($this->resource, $supportedInterlaceSchemes[$scheme]);

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

        static::getDriverInfo()->requirePaletteSupport($palette);

        $this->palette = $palette;

        return $this;
    }

    /**
     * Performs save or show operation using one of GD's image... functions.
     *
     * @param \Imagine\Image\Format $format
     * @param array $options
     * @param string $filename
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     * @throws \Imagine\Exception\RuntimeException
     */
    private function saveOrOutput(Format $format, array $options, $filename = null)
    {
        switch ($format->getID()) {
            default:
                $saveFunction = 'image' . $format->getID();
                break;
        }
        $args = array_merge(array(&$this->resource, $filename), $this->finalizeOptions($format, $options));

        ErrorHandling::throwingRuntimeException(E_WARNING | E_NOTICE, function () use ($saveFunction, $args) {
            if (call_user_func_array($saveFunction, $args) === false) {
                throw new RuntimeException('Save operation failed');
            }
        });
    }

    /**
     * @param \Imagine\Image\Format $format
     * @param array $options
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return array
     */
    private function finalizeOptions(Format $format, array $options)
    {
        $result = array();
        switch ($format->getID()) {
            case Format::ID_AVIF:
                // ranges from 0 (worst quality, smaller file) to 100 (best quality, larger file). If -1 is provided, the default value is used
                $quality = -1;
                // ranges from 0 (slow, smaller file) to 10 (fast, larger file). If -1 is provided, the default value is used
                $speed = -1;
                if (!empty($options['avif_lossless'])) {
                    $quality = 100;
                } else {
                    if (!isset($options['avif_quality'])) {
                        if (isset($options['quality'])) {
                            $options['avif_quality'] = $options['quality'];
                        }
                    }
                    if (isset($options['avif_quality'])) {
                        $quality = max(0, min(100, $options['avif_quality']));
                    }
                }
                $result[] = $quality;
                $result[] = $speed;
                break;
            case Format::ID_BMP:
                if (isset($options['compressed'])) {
                    $result[] = (bool) $options['compressed'];
                }
                break;
            case Format::ID_JPEG:
                if (!isset($options['jpeg_quality'])) {
                    if (isset($options['quality'])) {
                        $options['jpeg_quality'] = $options['quality'];
                    }
                }
                if (isset($options['jpeg_quality'])) {
                    $result[] = $options['jpeg_quality'];
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
                    $result[] = $options['png_compression_level'];
                } else {
                    $result[] = -1; // use default level
                }
                if (!isset($options['png_compression_filter'])) {
                    if (isset($options['filters'])) {
                        $options['png_compression_filter'] = $options['filters'];
                    }
                }
                if (isset($options['png_compression_filter'])) {
                    if (~PNG_ALL_FILTERS & $options['png_compression_filter']) {
                        throw new InvalidArgumentException('png_compression_filter option should be a combination of the PNG_FILTER_XXX constants');
                    }
                    $result[] = $options['png_compression_filter'];
                }
                break;
            case Format::ID_WEBP:
                if (!isset($options['webp_quality'])) {
                    if (isset($options['quality'])) {
                        $options['webp_quality'] = $options['quality'];
                    }
                }
                if (isset($options['webp_quality'])) {
                    if ($options['webp_quality'] < 0 || $options['webp_quality'] > 100) {
                        throw new InvalidArgumentException('webp_quality option should be an integer from 0 to 100');
                    }
                    $result[] = $options['webp_quality'];
                }
                break;
            case Format::ID_XBM:
            case Format::ID_WBMP:
                if (isset($options['foreground'])) {
                    $result[] = $options['foreground'];
                }
                break;
        }

        return $result;
    }

    /**
     * Generates a GD image.
     *
     * @param \Imagine\Image\BoxInterface $size
     * @param string $operation the operation initiating the creation
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return resource|\GdImage
     */
    private function createImage(BoxInterface $size, $operation)
    {
        $resource = imagecreatetruecolor($size->getWidth(), $size->getHeight());

        if ($resource === false) {
            throw new RuntimeException('Image ' . $operation . ' failed');
        }

        if (imagealphablending($resource, false) === false || imagesavealpha($resource, true) === false) {
            throw new RuntimeException('Image ' . $operation . ' failed');
        }

        if (function_exists('imageantialias')) {
            imageantialias($resource, true);
        }

        $transparent = imagecolorallocatealpha($resource, 255, 255, 255, 127);
        imagefill($resource, 0, 0, $transparent);
        imagecolortransparent($resource, $transparent);

        return $resource;
    }

    /**
     * Generates a GD color from Color instance.
     *
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     *
     * @throws \Imagine\Exception\RuntimeException
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return int A color identifier
     */
    private function getColor(ColorInterface $color)
    {
        if (!$color instanceof RGBColor) {
            throw new InvalidArgumentException('GD driver only supports RGB colors');
        }

        $index = imagecolorallocatealpha($this->resource, $color->getRed(), $color->getGreen(), $color->getBlue(), round(127 * (100 - $color->getAlpha()) / 100));

        if ($index === false) {
            throw new RuntimeException(sprintf('Unable to allocate color "RGB(%s, %s, %s)" with transparency of %d percent', $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha()));
        }

        return $index;
    }
}
