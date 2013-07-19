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

use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Palette\PaletteInterface;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\Color\RGB as RGBColor;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;

/**
 * Imagine implementation using the GD library
 */
final class Imagine implements ImagineInterface
{
    /**
     * @var array
     */
    private $info;

    /**
     * @throws RuntimeException
     */
    public function __construct()
    {
        $this->loadGdInfo();
        $this->requireGdVersion('2.0.1');
    }

    private function loadGdInfo()
    {
        if (!function_exists('gd_info')) {
            throw new RuntimeException('Gd not installed');
        }

        $this->info = gd_info();
    }

    private function requireGdVersion($version)
    {
        if (version_compare(GD_VERSION, $version, '<')) {
            throw new RuntimeException(sprintf('GD2 version %s or higher is required', $version));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(BoxInterface $size, ColorInterface $color = null)
    {
        $width  = $size->getWidth();
        $height = $size->getHeight();

        $resource = imagecreatetruecolor($width, $height);

        if (false === $resource) {
            throw new RuntimeException('Create operation failed');
        }

        $palette = null !== $color ? $color->getPalette() : new RGB();
        $color = $color ? $color : $palette->color('fff');

        if (!$color instanceof RGBColor) {
            throw new InvalidArgumentException(
                'GD driver only supports RGB colors'
            );
        }

        $index = imagecolorallocatealpha(
            $resource, $color->getRed(), $color->getGreen(), $color->getBlue(),
            round(127 * $color->getAlpha() / 100)
        );

        if (false === $index) {
            throw new RuntimeException('Unable to allocate color');
        }

        if (false === imagefill($resource, 0, 0, $index)) {
            throw new RuntimeException('Could not set background color fill');
        }

        if ($color->getAlpha() >= 95) {
            imagecolortransparent($resource, $index);
        }

        return $this->wrap($resource, $palette);
    }

    /**
     * {@inheritdoc}
     */
    public function open($path)
    {
        $handle = @fopen($path, 'r');

        if (false === $handle) {
            throw new InvalidArgumentException(sprintf(
                'File %s doesn\'t exist', $path
            ));
        }

        try {
            $image = $this->read($handle);
        } catch (\Exception $e) {
            fclose($handle);
            throw new RuntimeException(sprintf('Unable to open image %s', $path), $e->getCode(), $e);
        }

        return $image;
    }

    /**
     * {@inheritdoc}
     */
    public function load($string)
    {
        $resource = @imagecreatefromstring($string);

        if (!is_resource($resource)) {
            throw new InvalidArgumentException('An image could not be created from the given input');
        }

        return $this->wrap($resource, new RGB());
    }

    /**
     * {@inheritdoc}
     */
    public function read($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Variable does not contain a stream resource');
        }

        $content = stream_get_contents($resource);

        if (false === $content) {
            throw new InvalidArgumentException('Cannot read resource content');
        }

        return $this->load($content);
    }

    /**
     * {@inheritdoc}
     */
    public function font($file, $size, ColorInterface $color)
    {
        if (!$this->info['FreeType Support']) {
            throw new RuntimeException('GD is not compiled with FreeType support');
        }

        return new Font($file, $size, $color);
    }

    private function wrap($resource, PaletteInterface $palette)
    {
        if (!imageistruecolor($resource)) {
            list($width, $height) = array(imagesx($resource), imagesy($resource));

            // create transparent truecolor canvas
            $truecolor   = imagecreatetruecolor($width, $height);
            $transparent = imagecolorallocatealpha($truecolor, 255, 255, 255, 127);

            imagefill($truecolor, 0, 0, $transparent);
            imagecolortransparent($truecolor, $transparent);

            imagecopymerge($truecolor, $resource, 0, 0, 0, 0, $width, $height, 100);

            imagedestroy($resource);
            $resource = $truecolor;
        }

        if (false === imagealphablending($resource, false) ||
            false === imagesavealpha($resource, true)) {
            throw new RuntimeException(
                'Could not set alphablending, savealpha and antialias values'
            );
        }

        if (function_exists('imageantialias')) {
            imageantialias($resource, true);
        }

        return new Image($resource, $palette);
    }
}
