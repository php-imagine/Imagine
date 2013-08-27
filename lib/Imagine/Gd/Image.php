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

use Imagine\Image\AbstractImage;
use Imagine\Image\ImageInterface;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Fill\FillInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Imagine\Image\Palette\PaletteInterface;
use Imagine\Image\Palette\Color\RGB as RGBColor;
use Imagine\Image\ProfileInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;

/**
 * Image implementation using the GD library
 */
final class Image extends AbstractImage
{
    /**
     * @var resource
     */
    private $resource;
    private $layers;

    /**
     *
     * @var PaletteInterface
     */
    private $palette;

    /**
     * Path to original source file
     *
     * @var null|string
     */
    private $path;

    /**
     * Constructs a new Image instance using the result of
     * imagecreatetruecolor()
     *
     * @param resource         $resource
     * @param PaletteInterface $palette
     */
    public function __construct($resource, PaletteInterface $palette, $path = null)
    {
        $this->palette = $palette;
        $this->resource = $resource;
        $this->path = $path;
    }

    /**
     * Makes sure the current image resource is destroyed
     */
    public function __destruct()
    {
        if (is_resource($this->resource) && 'gd' === get_resource_type($this->resource)) {
            imagedestroy($this->resource);
        }
    }

    /**
     * Returns Gd resource
     *
     * @return resource
     */
    public function getGdResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    final public function copy()
    {
        $size = $this->getSize();

        $copy = $this->createImage($size, 'copy');

        if (false === imagecopy($copy, $this->resource, 0, 0, 0,
            0, $size->getWidth(), $size->getHeight())) {
            throw new RuntimeException('Image copy operation failed');
        }

        return new Image($copy, $this->palette);
    }

    /**
     * {@inheritdoc}
     */
    final public function crop(PointInterface $start, BoxInterface $size)
    {
        if (!$start->in($this->getSize())) {
            throw new OutOfBoundsException(
                'Crop coordinates must start at minimum 0, 0 position from '.
                'top  left corner, crop height and width must be positive '.
                'integers and must not exceed the current image borders'
            );
        }

        $width  = $size->getWidth();
        $height = $size->getHeight();

        $dest = $this->createImage($size, 'crop');

        if (false === imagecopy($dest, $this->resource, 0, 0,
            $start->getX(), $start->getY(), $width, $height)) {
            throw new RuntimeException('Image crop operation failed');
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function paste(ImageInterface $image, PointInterface $start)
    {
        if (!$image instanceof self) {
            throw new InvalidArgumentException(sprintf(
                'Gd\Image can only paste() Gd\Image instances, %s given',
                get_class($image)
            ));
        }

        $size = $image->getSize();
        if (!$this->getSize()->contains($size, $start)) {
            throw new OutOfBoundsException(
                'Cannot paste image of the given size at the specified '.
                'position, as it moves outside of the current image\'s box'
            );
        }

        imagealphablending($this->resource, true);
        imagealphablending($image->resource, true);

        if (false === imagecopy($this->resource, $image->resource, $start->getX(), $start->getY(),
            0, 0, $size->getWidth(), $size->getHeight())) {
            throw new RuntimeException('Image paste operation failed');
        }

        imagealphablending($this->resource, false);
        imagealphablending($image->resource, false);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function resize(BoxInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        if (ImageInterface::FILTER_UNDEFINED !== $filter) {
            throw new InvalidArgumentException('Unsupported filter type, GD only supports ImageInterface::FILTER_UNDEFINED filter');
        }

        $width  = $size->getWidth();
        $height = $size->getHeight();

        $dest = $this->createImage($size, 'resize');

        imagealphablending($this->resource, true);
        imagealphablending($dest, true);

        if (false === imagecopyresampled($dest, $this->resource, 0, 0, 0, 0,
            $width, $height, imagesx($this->resource), imagesy($this->resource)
        )) {
            throw new RuntimeException('Image resize operation failed');
        }

        imagealphablending($this->resource, false);
        imagealphablending($dest, false);

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function rotate($angle, ColorInterface $background = null)
    {
        $color = $background ? $background : $this->palette->color('fff');

        $resource = imagerotate($this->resource, -1 * $angle, $this->getColor($color));

        if (false === $resource) {
            throw new RuntimeException('Image rotate operation failed');
        }

        imagedestroy($this->resource);

        $this->resource = $resource;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function save($path = null, array $options = array())
    {
        $path = null === $path ? $this->path : $path;

        if (null === $path) {
            throw new RuntimeException(
                'You can omit save path only if image has been open from a file'
            );
        }

        if (isset($options['format'])) {
            $format = $options['format'];
        } elseif ('' !== $extension = pathinfo($path, \PATHINFO_EXTENSION)) {
            $format = $extension;
        } else {
            $format = pathinfo($this->path, \PATHINFO_EXTENSION);
        }

        $this->saveOrOutput($format, $options, $path);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function show($format, array $options = array())
    {
        header('Content-type: '.$this->getMimeType($format));

        $this->saveOrOutput($format, $options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($format, array $options = array())
    {
        ob_start();
        $this->saveOrOutput($format, $options);

        return ob_get_clean();
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
    final public function flipHorizontally()
    {
        $size   = $this->getSize();
        $width  = $size->getWidth();
        $height = $size->getHeight();
        $dest   = $this->createImage($size, 'flip');

        for ($i = 0; $i < $width; $i++) {
            if (false === imagecopy($dest, $this->resource, $i, 0,
                ($width - 1) - $i, 0, 1, $height)) {
                throw new RuntimeException('Horizontal flip operation failed');
            }
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function flipVertically()
    {
        $size   = $this->getSize();
        $width  = $size->getWidth();
        $height = $size->getHeight();
        $dest   = $this->createImage($size, 'flip');

        for ($i = 0; $i < $height; $i++) {
            if (false === imagecopy($dest, $this->resource, 0, $i,
                0, ($height - 1) - $i, $width, 1)) {
                throw new RuntimeException('Vertical flip operation failed');
            }
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function strip()
    {
        /**
         * GD strips profiles and comment, so there's nothing to do here
         */

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function draw()
    {
        return new Drawer($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function effects()
    {
        return new Effects($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return new Box(imagesx($this->resource), imagesy($this->resource));
    }

    /**
     * {@inheritdoc}
     */
    public function applyMask(ImageInterface $mask)
    {
        if (!$mask instanceof self) {
            throw new InvalidArgumentException('Cannot mask non-gd images');
        }

        $size     = $this->getSize();
        $maskSize = $mask->getSize();

        if ($size != $maskSize) {
            throw new InvalidArgumentException(sprintf(
                'The given mask doesn\'t match current image\'s size, Current '.
                'mask\'s dimensions are %s, while image\'s dimensions are %s',
                $maskSize, $size
            ));
        }

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x++) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y++) {
                $position  = new Point($x, $y);
                $color     = $this->getColorAt($position);
                $maskColor = $mask->getColorAt($position);

                $round     = (int) round(max($color->getAlpha(), (100 - $color->getAlpha()) * $maskColor->getRed() / 255));

                if (false === imagesetpixel(
                    $this->resource,
                    $x, $y,
                    $this->getColor($color->dissolve($round - $color->getAlpha()))
                )) {
                    throw new RuntimeException('Apply mask operation failed');
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fill(FillInterface $fill)
    {
        $size = $this->getSize();

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x++) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y++) {
                if (false === imagesetpixel(
                    $this->resource,
                    $x, $y,
                    $this->getColor($fill->getColor(new Point($x, $y))))
                ) {
                    throw new RuntimeException('Fill operation failed');
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function mask()
    {
        $mask = $this->copy();

        if (false === imagefilter($mask->resource, IMG_FILTER_GRAYSCALE)) {
            throw new RuntimeException('Mask operation failed');
        }

        return $mask;
    }

    /**
     * {@inheritdoc}
     */
    public function histogram()
    {
        $size   = $this->getSize();
        $colors = array();

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x++) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y++) {
                $colors[] = $this->getColorAt(new Point($x, $y));
            }
        }

        return array_unique($colors);
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
        $index = imagecolorat($this->resource, $point->getX(), $point->getY());
        $info  = imagecolorsforindex($this->resource, $index);

        return $this->palette->color(array(
                $info['red'],
                $info['green'],
                $info['blue'],
            ),
            (int) round($info['alpha'] / 127 * 100)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function layers()
    {
        if (null === $this->layers) {
            $this->layers = new Layers($this, $this->palette, $this->resource);
        }

        return $this->layers;
    }

    /**
     * {@inheritdoc}
     **/
    public function interlace($scheme)
    {
        static $supportedInterlaceSchemes = array(
            ImageInterface::INTERLACE_NONE      => 0,
            ImageInterface::INTERLACE_LINE      => 1,
            ImageInterface::INTERLACE_PLANE     => 1,
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
        throw new RuntimeException('GD driver does not support color profiles');
    }

    /**
     * {@inheritdoc}
     */
    public function usePalette(PaletteInterface $palette)
    {
        if (!$palette instanceof RGB) {
            throw new RuntimeException('GD driver only supports RGB palette');
        }

        $this->palette = $palette;

        return $this;
    }

    /**
     * Internal
     *
     * Performs save or show operation using one of GD's image... functions
     *
     * @param string $format
     * @param array  $options
     * @param string $filename
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    private function saveOrOutput($format, array $options, $filename = null)
    {

        if (!$this->supported($format)) {
            throw new InvalidArgumentException(sprintf(
                'Saving image in "%s" format is not supported, please use one '.
                'of the following extension: "%s"', $format,
                implode('", "', $this->supported())
            ));
        }

        $save = 'image'.$format;
        $args = array(&$this->resource, $filename);

        if (($format === 'jpeg' || $format === 'png') &&
            isset($options['quality'])) {
            // Png compression quality is 0-9, so here we get the value from percent.
            // Beaware that compression level for png works the other way around.
            // For PNG 0 means no compression and 9 means highest compression level.
            if ($format === 'png') {
                $options['quality'] = round((100 - $options['quality']) * 9 / 100);
            }
            $args[] = $options['quality'];
        }

        if ($format === 'png' && isset($options['filters'])) {
            $args[] = $options['filters'];
        }

        if (($format === 'wbmp' || $format === 'xbm') &&
            isset($options['foreground'])) {
            $args[] = $options['foreground'];
        }

        $this->setExceptionHandler();

        if (false === call_user_func_array($save, $args)) {
            throw new RuntimeException('Save operation failed');
        }

        $this->resetExceptionHandler();
    }

    /**
     * Internal
     *
     * Generates a GD image
     *
     * @param BoxInterface $size
     * @param  string the operation initiating the creation
     *
     * @return resource
     *
     * @throws RuntimeException
     *
     */
    private function createImage(BoxInterface $size, $operation)
    {
        $resource = imagecreatetruecolor($size->getWidth(), $size->getHeight());

        if (false === $resource) {
            throw new RuntimeException('Image '.$operation.' failed');
        }

        if (false === imagealphablending($resource, false) ||
            false === imagesavealpha($resource, true)) {
            throw new RuntimeException('Image '.$operation.' failed');
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
     * Internal
     *
     * Generates a GD color from Color instance
     *
     * @param Color $color
     *
     * @return resource
     *
     * @throws RuntimeException
     */
    private function getColor(ColorInterface $color)
    {
        if (!$color instanceof RGBColor) {
            throw new InvalidArgumentException(
                'GD driver only supports RGB colors'
            );
        }

        $index = imagecolorallocatealpha(
            $this->resource, $color->getRed(), $color->getGreen(),
            $color->getBlue(), round(127 * $color->getAlpha() / 100)
        );

        if (false === $index) {
            throw new RuntimeException(sprintf(
                'Unable to allocate color "RGB(%s, %s, %s)" with transparency '.
                'of %d percent', $color->getRed(), $color->getGreen(),
                $color->getBlue(), $color->getAlpha()
            ));
        }

        return $index;
    }

    /**
     * Internal
     *
     * Checks whether a given format is supported by GD library
     *
     * @param string $format
     *
     * @return Boolean
     */
    private function supported(&$format = null)
    {
        $formats = array('gif', 'jpeg', 'png', 'wbmp', 'xbm');

        if (null === $format) {
            return $formats;
        }

        $format  = strtolower($format);

        if ('jpg' === $format || 'pjpeg' === $format) {
            $format = 'jpeg';
        }

        return in_array($format, $formats);
    }

    private function setExceptionHandler()
    {
        set_error_handler(function($errno, $errstr, $errfile, $errline) {

            if (0 === error_reporting()) {
                return;
            }

            throw new RuntimeException(
                $errstr, $errno,
                new \ErrorException($errstr, 0, $errno, $errfile, $errline)
            );
        }, E_WARNING | E_NOTICE);
    }

    private function resetExceptionHandler()
    {
        restore_error_handler();
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
        if (!$this->supported($format)) {
            throw new RuntimeException('Invalid format');
        }

        static $mimeTypes = array(
            'jpeg' => 'image/jpeg',
            'jpg'  => 'image/jpeg',
            'gif'  => 'image/gif',
            'png'  => 'image/png',
            'wbmp' => 'image/vnd.wap.wbmp',
            'xbm'  => 'image/xbm',
        );

        return $mimeTypes[$format];
    }
}
