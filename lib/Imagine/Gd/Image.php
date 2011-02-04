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

use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;
use Imagine\Color;
use Imagine\ImageInterface;
use Imagine\ImageMetadataInterface;

class Image implements ImageInterface, ImageMetadataInterface
{
    protected $width, $height;

    protected $resource;

    /**
     * Constructs a new Image instance using the result of
     * imagecreatetruecolor()
     *
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
        $this->width    = imagesx($resource);
        $this->height   = imagesy($resource);
    }

    public function __destruct()
    {
        imagedestroy($this->resource);
    }

    final public function getHeight()
    {
        return $this->height;
    }

    final public function getWidth()
    {
        return $this->width;
    }

    final public function copy()
    {
        $copy = new BlankImage($this->width, $this->height);

        if (!imagecopy($copy->resource, $this->resource, 0, 0, 0, 0,
            $this->width, $this->height)) {
            throw new RuntimeException('Image copy operation failed');
        }

        return $copy;
    }

    final public function crop($x, $y, $width, $height)
    {
        $dest = imagecreatetruecolor();

        if ($x < 0 || $y < 0 || $width < 1 || $height < 1 ||
            $this->width - ($x + $width) < 0 ||
            $this->height - ($y + $height) < 0) {
            throw new OutOfBoundsException('Crop coordinates must start at '.
                'minimum 0, 0 position from top left corner, crop height and '.
                'width must be positive integers and must not exceed the '.
                'current image borders');
        }

        if (!imagecopy($dest, $this->resource, 0, 0, $x, $y, $width, $height)) {
            throw new RuntimeException('Image crop operation failed');
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    final public function paste(ImageInterface $image, $x, $y)
    {
        if (!$image instanceof self) {
            throw new InvalidArgumentException(sprintf('Gd\Image can only '.
                'paste() Gd\Image instances, %s given', get_class($image)));
        }

        $widthDiff = $this->width - ($x + $image->getWidth());
        $heightDiff = $this->height - ($y + $image->getHeight());

        if ($widthDiff < 0 || $heightDiff < 0) {
            throw new OutOfBoundsException(sprintf('Cannot paste image '.
                'of width %d and height %d at the x position of %d and y '.
                'position of %d, as it exceeds the parent image\'s width by '.
                '%d in width and %d in height', $image->getWidth(),
                $image->getHeight(), $x, $y, abs($widthDiff), abs($heightDiff)
            ));
        }

        if (!imagecopy($this->resource, $image->resource, $x, $y, 0, 0,
            $image->getWidth(), $image->getHeight())) {
            throw new RuntimeException('Image paste operation failed');
        }

        return $this;
    }

    final public function resize($width, $height)
    {
        $dest = imagecreatetruecolor($width, $height);

        if (!imagecopyresampled($dest, $this->resource, 0, 0, 0, 0, $width,
            $height, $this->width, $this->height)) {
            throw new RuntimeException('Image resize operation failed');
        }

        imagedestroy($this->resource);

        $this->width    = $width;
        $this->height   = $height;
        $this->resource = $dest;

        return $this;
    }

    final public function rotate($angle, Color $background = null)
    {
        $color = $background ? $background : new Color('fff');

        if (!($resource = imagerotate($this->resource, $angle, imagecolorexact(
            $this->resource, $color->getRed(), $color->getGreen(),
            $color->getBlue())))) {
            throw new RuntimeException('Image rotate operation failed');
        }

        imagedestroy($this->resource);
        $this->resource = $resource;

        return $this;
    }

    final public function save($path, array $options = array())
    {
        $info = pathinfo($path);

        $format = $info['extension'];

        $this->saveOrOutput($format, $options, $path);

        return $this;
    }

    final public function show($format, array $options = array())
    {
        $this->saveOrOutput($format, $options);

        return $this;
    }

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
            $args[] = $options['quality'];
        }

        if ($format === 'png' && isset($options['filters'])) {
            $args[] = $options['filters'];
        }

        if (($format === 'wbmp' || $format === 'xbm') && isset($options['foreground'])) {
            $args[] = $options['foreground'];
        }

        if (!call_user_func_array($save, $args)) {
            throw new RuntimeException('Save operation failed');
        }
    }

    final public function flipHorizontally()
    {
        $dest = imagecreatetruecolor($this->width, $this->height);

        for ($i = 0; $i < $this->width; $i++) {
            if (!imagecopy($dest, $this->resource, $i, 0,
                ($this->width - 1) - $i, 0, 1, $this->height)) {
                throw new RuntimeException('Horizontal flip operation failed');
            }
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    final public function flipVertically()
    {
        $dest = imagecreatetruecolor($this->width, $this->height);

        for ($i = 0; $i < $this->height; $i++) {
            if (!imagecopy($dest, $this->resource, 0, $i,
                0, ($this->height - 1) - $i, $this->width, 1)) {
                throw new RuntimeException('Vartical flip operation failed');
            }
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;

    }

    protected function supported(&$format = null)
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
