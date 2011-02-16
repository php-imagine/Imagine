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

use Imagine\Cartesian\Coordinate\Center;

use Imagine\Color;
use Imagine\Cartesian\Coordinate;
use Imagine\Cartesian\CoordinateInterface;
use Imagine\Cartesian\Size;
use Imagine\Cartesian\SizeInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;
use Imagine\Gd\Imagine;
use Imagine\ImageInterface;

final class Image implements ImageInterface
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * @var Imagine
     */
    private $imagine;

    /**
     * Constructs a new Image instance using the result of
     * imagecreatetruecolor()
     *
     * @param resource $resource
     * @param integer  $width
     * @param integer  $height
     */
    public function __construct($resource, Imagine $imagine)
    {
        $this->resource = $resource;
        $this->imagine  = $imagine;
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
     * @see Imagine.ImageInterface::copy()
     */
    final public function copy()
    {
        $size = $this->getSize();
        $copy = $this->imagine->create($size);

        if (false === imagecopymerge($copy->resource, $this->resource, 0, 0, 0,
            0, $size->getWidth(), $size->getHeight(), 100)) {
            throw new RuntimeException('Image copy operation failed');
        }

        return $copy;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::crop()
     */
    final public function crop(CoordinateInterface $start, SizeInterface $size)
    {
        if (!$start->in($size)) {
            throw new OutOfBoundsException('Crop coordinates must start at '.
                'minimum 0, 0 position from top left corner, crop height and '.
                'width must be positive integers and must not exceed the '.
                'current image borders');
        }

        $width  = $size->getWidth();
        $height = $size->getHeight();

        $dest = imagecreatetruecolor($width, $height);

        imagealphablending($dest, false);
        imagesavealpha($dest, true);

        if (false === imagecopymerge($dest, $this->resource, 0, 0,
            $start->getX(), $start->getY(), $width, $height, 100)) {
            throw new RuntimeException('Image crop operation failed');
        }

        imagedestroy($this->resource);

        $this->width    = $width;
        $this->height   = $height;
        $this->resource = $dest;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::paste()
     */
    final public function paste(ImageInterface $image, CoordinateInterface $start)
    {
        if (!$image instanceof self) {
            throw new InvalidArgumentException(sprintf('Gd\Image can only '.
                'paste() Gd\Image instances, %s given', get_class($image)));
        }

        $size = $image->getSize();
        if (!$this->getSize()->contains($size, $start)) {
            throw new OutOfBoundsException(
                'Cannot paste image of the given size at the specified '.
                'position, as it moves outside of the current image\'s box'
            );
        }

        if (false === imagecopymerge($this->resource, $image->resource, $start->getX(), $start->getY(),
            0, 0, $size->getWidth(), $size->getHeight(), 100)) {
            throw new RuntimeException('Image paste operation failed');
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::resize()
     */
    final public function resize(SizeInterface $size)
    {
        $width  = $size->getWidth();
        $height = $size->getHeight();

        $dest = imagecreatetruecolor($width, $height);

        imagealphablending($dest, false);
        imagesavealpha($dest, true);

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
     * @see Imagine.ImageInterface::rotate()
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

        $this->width    = imagesx($resource);
        $this->height   = imagesy($resource);
        $this->resource = $resource;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::save()
     */
    final public function save($path, array $options = array())
    {
        $this->saveOrOutput(pathinfo($path, \PATHINFO_EXTENSION), $options, $path);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::show()
     */
    final public function show($format, array $options = array())
    {
        $this->saveOrOutput($format, $options);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::flipHorizontally()
     */
    final public function flipHorizontally()
    {
        $width  = imagesx($this->resource);
        $height = imagesy($this->resource);
        $dest = imagecreatetruecolor($width, $height);

        imagealphablending($dest, false);
        imagesavealpha($dest, true);

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
     * @see Imagine.ImageInterface::flipVertically()
     */
    final public function flipVertically()
    {
        $width  = imagesx($this->resource);
        $height = imagesy($this->resource);
        $dest   = imagecreatetruecolor($width, $height);

        imagealphablending($dest, false);
        imagesavealpha($dest, true);

        for ($i = 0; $i < $height; $i++) {
            if (false === imagecopymerge($dest, $this->resource, 0, $i,
                0, ($height - 1) - $i, $width, 1, 100)) {
                throw new RuntimeException('Vartical flip operation failed');
            }
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::thumbnail()
     */
    public function thumbnail(SizeInterface $size, $mode = ImageInterface::THUMBNAIL_INSET)
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
            $thumbnail->crop(new Coordinate(
                round(($thumbnailSize->getWidth() - $width) / 2),
                round(($thumbnailSize->getHeight() - $height) / 2)
            ), $size);
        }

        return $thumbnail;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::draw()
     */
    public function draw()
    {
        return new Drawer($this->resource);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::getSize()
     */
    public function getSize()
    {
        return new Size(imagesx($this->resource), imagesy($this->resource));
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
            throw new InvalidArgumentException(sprintf('Saving image in "%s" '.
                'format is not supported, please use one of the following '.
                'extension: "%s"', $format, implode('", "', $this->supported())
            ));
        }

        $save = 'image'.$format;

        $args = array($this->resource);

        if (null !== $filename) {
            $args[] = $filename;
        }

        if (($format === 'jpeg' || $format === 'png') && isset($options['quality'])) {
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

        if (($format === 'wbmp' || $format === 'xbm') && isset($options['foreground'])) {
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
     * @param  Color $color
     * @throws RuntimeException
     *
     * @return resource
     */
    private function getColor(Color $color)
    {
        $color = imagecolorallocatealpha($this->resource, $color->getRed(),
            $color->getGreen(), $color->getBlue(),
            round(127 * $color->getAlpha() / 100));
        if (false === $color) {
            throw new RuntimeException(sprintf('Unable to allocate color '.
                '"RGB(%s, %s, %s)" with transparency of %d percent',
                $color->getRed(), $color->getGreen(), $color->getBlue(),
                $color->getAlpha()));
        }

        return $color;
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

        if ('jpg' === $format) {
            $format = 'jpeg';
        }

        return in_array($format, $formats);
    }
}
