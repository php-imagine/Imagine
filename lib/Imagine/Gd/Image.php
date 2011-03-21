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

use Imagine\ImageInterface;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Imagine\Image\Point\Center;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;
use Imagine\Fill\FillInterface;
use Imagine\Gd\Imagine;
use Imagine\Mask\MaskInterface;

final class Image implements ImageInterface
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * Constructs a new Image instance using the result of
     * imagecreatetruecolor()
     *
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Makes sure the current image resource is destroyed
     */
    public function __destruct()
    {
        imagedestroy($this->resource);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::copy()
     */
    final public function copy()
    {
        $size = $this->getSize();
        $copy = imagecreatetruecolor($size->getWidth(), $size->getHeight());

        if (false === $copy) {
            throw new RuntimeException('Image copy operation failed');
        }

        if (false === imagealphablending($copy, false) ||
            false === imagesavealpha($copy, true)) {
            throw new RuntimeException('Image copy operation failed');
        }

        if (function_exists('imageantialias')) {
            imageantialias($copy, true);
        }

        if (false === imagecopymerge($copy, $this->resource, 0, 0, 0,
            0, $size->getWidth(), $size->getHeight(), 100)) {
            throw new RuntimeException('Image copy operation failed');
        }

        return new Image($copy);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::crop()
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

        $dest = imagecreatetruecolor($width, $height);

        imagealphablending($dest, false);
        imagesavealpha($dest, true);

        if (function_exists('imageantialias')) {
            imageantialias($dest, true);
        }

        if (false === imagecopymerge($dest, $this->resource, 0, 0,
            $start->getX(), $start->getY(), $width, $height, 100)) {
            throw new RuntimeException('Image crop operation failed');
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::paste()
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
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::resize()
     */
    final public function resize(BoxInterface $size)
    {
        $width  = $size->getWidth();
        $height = $size->getHeight();

        $dest = imagecreatetruecolor($width, $height);

        imagealphablending($dest, false);
        imagesavealpha($dest, true);

        if (function_exists('imageantialias')) {
            imageantialias($dest, true);
        }

        if (false === imagecopyresampled($dest, $this->resource, 0, 0, 0, 0,
            $width, $height, imagesx($this->resource), imagesy($this->resource)
        )) {
            throw new RuntimeException('Image resize operation failed');
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::rotate()
     */
    final public function rotate($angle, Color $background = null)
    {
        $color = $background ? $background : new Color('fff');

        $resource = imagerotate($this->resource, $angle,
            $this->getColor($background));

        if (false === $resource) {
            throw new RuntimeException('Image rotate operation failed');
        }

        imagedestroy($this->resource);

        $this->resource = $resource;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::save()
     */
    final public function save($path, array $options = array())
    {
        $this->saveOrOutput(pathinfo($path, \PATHINFO_EXTENSION), $options, $path);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::show()
     */
    public function show($format, array $options = array())
    {
        $this->saveOrOutput($format, $options);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::get()
     */
    public function get($format, array $options = array())
    {
        ob_start();
        $this->saveOrOutput($format, $options);
        return ob_get_clean();
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
     * @see Imagine\ImageInterface::flipHorizontally()
     */
    final public function flipHorizontally()
    {
        $width  = imagesx($this->resource);
        $height = imagesy($this->resource);
        $dest = imagecreatetruecolor($width, $height);

        imagealphablending($dest, false);
        imagesavealpha($dest, true);

        if (function_exists('imageantialias')) {
            imageantialias($dest, true);
        }

        for ($i = 0; $i < $width; $i++) {
            if (false === imagecopymerge($dest, $this->resource, $i, 0,
                ($width - 1) - $i, 0, 1, $height, 100)) {
                throw new RuntimeException('Horizontal flip operation failed');
            }
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::flipVertically()
     */
    final public function flipVertically()
    {
        $width  = imagesx($this->resource);
        $height = imagesy($this->resource);
        $dest   = imagecreatetruecolor($width, $height);

        imagealphablending($dest, false);
        imagesavealpha($dest, true);

        if (function_exists('imageantialias')) {
            imageantialias($dest, true);
        }

        for ($i = 0; $i < $height; $i++) {
            if (false === imagecopymerge($dest, $this->resource, 0, $i,
                0, ($height - 1) - $i, $width, 1, 100)) {
                throw new RuntimeException('Vertical flip operation failed');
            }
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
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

        $width  = $size->getWidth();
        $height = $size->getHeight();

        $ratios = array(
            $width / imagesx($this->resource),
            $height / imagesy($this->resource)
        );

        $thumbnail = $this->copy();

        if ($mode === ImageInterface::THUMBNAIL_INSET) {
            $ratio = min($ratios);
        } else if ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
            $ratio = max($ratios);
        }

        $thumbnail->resize($this->getSize()->scale($ratio));
        $thumbnailSize = $thumbnail->getSize();

        if ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
            $thumbnail->crop(new Point(
                max(0, round(($thumbnailSize->getWidth() - $width) / 2)),
                max(0, round(($thumbnailSize->getHeight() - $height) / 2))
            ), $size);
        }

        return $thumbnail;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::draw()
     */
    public function draw()
    {
        return new Drawer($this->resource);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::getSize()
     */
    public function getSize()
    {
        return new Box(imagesx($this->resource), imagesy($this->resource));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::applyMask()
     */
    public function applyMask(ImageInterface $mask)
    {
        if (!$mask instanceof self) {
            throw new InvalidArgumentException('Cannot mask non-gd images');
        }

        $size = $this->getSize();
        $maskSize = $mask->getSize();

        if ($size != $maskSize) {
            throw new InvalidArgumentException(sprintf(
                'The given mask doesn\'t match current image\'s sise, Current '.
                'mask\'s dimensions are %s, while image\'s dimensions are %s',
                $maskSize, $size
            ));
        }

        for ($x = 0; $x < $size->getWidth(); $x++) {
            for ($y = 0; $y < $size->getHeight(); $y++) {
                $color     = imagecolorat($this->resource, $x, $y);
                $info      = imagecolorsforindex($this->resource, $color);
                $maskColor = $color = imagecolorat($mask->resource, $x, $y);
                $maskInfo  = imagecolorsforindex($mask->resource, $maskColor);
                if (false === imagesetpixel(
                    $this->resource,
                    $x, $y,
                    imagecolorallocatealpha(
                        $this->resource,
                        $info['red'],
                        $info['green'],
                        $info['blue'],
                        round((127 - $info['alpha']) * $maskInfo['red'] / 255)
                    )
                )) {
                    throw new RuntimeException('Apply mask operation failed');
                }
            }
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::fill()
     */
    public function fill(FillInterface $fill)
    {
        $size = $this->getSize();

        for ($x = 0; $x < $size->getWidth(); $x++) {
            for ($y = 0; $y < $size->getHeight(); $y++) {
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
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::mask()
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
     * (non-PHPdoc)
     * @see Imagine\ImageInterface::histogram()
     */
    public function histogram()
    {
        $size   = $this->getSize();
        $colors = array();

        for ($x = 0; $x < $size->getWidth(); $x++) {
            for ($y = 0; $y < $size->getHeight(); $y++) {
                $index = imagecolorat($this->resource, $x, $y);
                $info  = imagecolorsforindex($this->resource, $index);
                $color = new Color(array(
                        $info['red'],
                        $info['green'],
                        $info['blue'],
                    ),
                    (int) round($info['alpha'] / 127 * 100)
                );

                $colors[] = $color;
            }
        }

        return array_unique($colors);
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

        $args = array($this->resource, $filename);

        if (($format === 'jpeg' || $format === 'png') &&
            isset($options['quality'])) {
            // png compression quality is 0-9, so here we get the value from percent
            if ($format === 'png') {
                $options['quality'] = round($options['quality'] * 9 / 100);
            }
            $args[] = $options['quality'];
        }

        if ($format === 'png') {
            imagealphablending($this->resource, false);
            imagesavealpha($this->resource, true);

            if (isset($options['filters'])) {
                $args[] = $options['filters'];
            }
        }

        if (($format === 'wbmp' || $format === 'xbm') &&
            isset($options['foreground'])) {
            $args[] = $options['foreground'];
        }

        if (false === call_user_func_array($save, $args)) {
            throw new RuntimeException('Save operation failed');
        }
    }

    /**
     * Internal
     *
     * Generates a GD color from Color instance
     *
     * @param  Imagine\Image\Color $color
     *
     * @return resource
     *
     * @throws Imagine\Exception\RuntimeException
     */
    private function getColor(Color $color)
    {
        $c = imagecolorallocatealpha(
            $this->resource, $color->getRed(), $color->getGreen(),
            $color->getBlue(), round(127 * $color->getAlpha() / 100)
        );
        if (false === $color) {
            throw new RuntimeException(sprintf(
                'Unable to allocate color "RGB(%s, %s, %s)" with transparency '.
                'of %d percent', $color->getRed(), $color->getGreen(),
                $color->getBlue(), $color->getAlpha()
            ));
        }

        return $c;
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

        if ('jpg' === $format) {
            $format = 'jpeg';
        }

        return in_array($format, $formats);
    }
}
