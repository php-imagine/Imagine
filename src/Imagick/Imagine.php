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

use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\NotSupportedException;
use Imagine\Exception\RuntimeException;
use Imagine\Factory\ClassFactoryInterface;
use Imagine\File\LoaderInterface;
use Imagine\Image\AbstractImagine;
use Imagine\Image\BoxInterface;
use Imagine\Image\Metadata\MetadataBag;
use Imagine\Image\Palette\CMYK;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Grayscale;
use Imagine\Image\Palette\RGB;
use Imagine\Utils\ErrorHandling;

/**
 * Imagine implementation using the Imagick PHP extension.
 */
final class Imagine extends AbstractImagine
{
    /**
     * @throws \Imagine\Exception\RuntimeException
     */
    public function __construct()
    {
        if (!class_exists('Imagick')) {
            throw new RuntimeException('Imagick not installed');
        }

        $version = $this->getVersion(new \Imagick());

        if (version_compare('6.2.9', $version) > 0) {
            throw new RuntimeException(sprintf('ImageMagick version 6.2.9 or higher is required, %s provided', $version));
        }
        if ($version === '7.0.7-32') { // https://github.com/avalanche123/Imagine/issues/689
            throw new RuntimeException(sprintf('ImageMagick version %s has known bugs that prevent it from working', $version));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImagineInterface::open()
     */
    public function open($path)
    {
        $loader = $path instanceof LoaderInterface ? $path : $this->getClassFactory()->createFileLoader($path);
        $path = $loader->getPath();

        try {
            if ($loader->isLocalFile()) {
                if (DIRECTORY_SEPARATOR === '\\' && PHP_INT_SIZE === 8 && PHP_VERSION_ID >= 70100 && PHP_VERSION_ID < 70200) {
                    $imagick = new \Imagick();
                    // PHP 7.1 64 bit on Windows: don't pass the file name to the constructor: it may break PHP - see https://github.com/mkoppanen/imagick/issues/252
                    $imagick->readImageBlob($loader->getData(), $path);
                } else {
                    $imagick = new \Imagick($loader->getPath());
                }
            } else {
                $imagick = new \Imagick();
                $imagick->readImageBlob($loader->getData());
            }
            $image = $this->getClassFactory()->createImage(ClassFactoryInterface::HANDLE_IMAGICK, $imagick, $this->createPalette($imagick), $this->getMetadataReader()->readFile($loader));
        } catch (\ImagickException $e) {
            throw new RuntimeException(sprintf('Unable to open image %s', $path), $e->getCode(), $e);
        }

        return $image;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImagineInterface::create()
     */
    public function create(BoxInterface $size, ColorInterface $color = null)
    {
        $width = $size->getWidth();
        $height = $size->getHeight();

        $palette = null !== $color ? $color->getPalette() : new RGB();
        $color = null !== $color ? $color : $palette->color('fff');

        try {
            $pixel = new \ImagickPixel((string) $color);
            $pixel->setColorValue(\Imagick::COLOR_ALPHA, $color->getAlpha() / 100);

            $imagick = new \Imagick();
            $imagick->newImage($width, $height, $pixel);
            $imagick->setImageMatte(true);
            $imagick->setImageBackgroundColor($pixel);

            if (version_compare('6.3.1', $this->getVersion($imagick)) < 0) {
                // setImageOpacity was replaced with setImageAlpha in php-imagick v3.4.3
                if (method_exists($imagick, 'setImageAlpha')) {
                    $imagick->setImageAlpha($pixel->getColorValue(\Imagick::COLOR_ALPHA));
                } else {
                    ErrorHandling::ignoring(E_DEPRECATED, function () use ($imagick, $pixel) {
                        $imagick->setImageOpacity($pixel->getColorValue(\Imagick::COLOR_ALPHA));
                    });
                }
            }

            $pixel->clear();
            $pixel->destroy();

            return $this->getClassFactory()->createImage(ClassFactoryInterface::HANDLE_IMAGICK, $imagick, $palette, new MetadataBag());
        } catch (\ImagickException $e) {
            throw new RuntimeException('Could not create empty image', $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImagineInterface::load()
     */
    public function load($string)
    {
        try {
            $imagick = new \Imagick();

            $imagick->readImageBlob($string);
            $imagick->setImageMatte(true);

            return $this->getClassFactory()->createImage(ClassFactoryInterface::HANDLE_IMAGICK, $imagick, $this->createPalette($imagick), $this->getMetadataReader()->readData($string));
        } catch (\ImagickException $e) {
            throw new RuntimeException('Could not load image from string', $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImagineInterface::read()
     */
    public function read($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Variable does not contain a stream resource');
        }

        $content = stream_get_contents($resource);

        try {
            $imagick = new \Imagick();
            $imagick->readImageBlob($content);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Could not read image from resource', $e->getCode(), $e);
        }

        return $this->getClassFactory()->createImage(ClassFactoryInterface::HANDLE_IMAGICK, $imagick, $this->createPalette($imagick), $this->getMetadataReader()->readData($content, $resource));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImagineInterface::font()
     */
    public function font($file, $size, ColorInterface $color)
    {
        return $this->getClassFactory()->createFont(ClassFactoryInterface::HANDLE_IMAGICK, $file, $size, $color);
    }

    /**
     * Returns the palette corresponding to an \Imagick resource colorspace.
     *
     * @param \Imagick $imagick
     *
     * @throws \Imagine\Exception\NotSupportedException
     *
     * @return \Imagine\Image\Palette\CMYK|\Imagine\Image\Palette\Grayscale|\Imagine\Image\Palette\RGB
     */
    private function createPalette(\Imagick $imagick)
    {
        switch ($imagick->getImageColorspace()) {
            case \Imagick::COLORSPACE_RGB:
            case \Imagick::COLORSPACE_SRGB:
                return new RGB();
            case \Imagick::COLORSPACE_CMYK:
                return new CMYK();
            case \Imagick::COLORSPACE_GRAY:
                return new Grayscale();
            default:
                throw new NotSupportedException('Only RGB and CMYK colorspace are currently supported');
        }
    }

    /**
     * Returns ImageMagick version.
     *
     * @param \Imagick $imagick
     *
     * @return string
     */
    private function getVersion(\Imagick $imagick)
    {
        $v = $imagick->getVersion();
        list($version) = sscanf($v['versionString'], 'ImageMagick %s %04d-%02d-%02d %s %s');

        return $version;
    }
}
