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

use Imagine\Driver\InfoProvider;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\NotSupportedException;
use Imagine\Exception\RuntimeException;
use Imagine\Factory\ClassFactoryInterface;
use Imagine\File\LoaderInterface;
use Imagine\Image\AbstractImagine;
use Imagine\Image\BoxInterface;
use Imagine\Image\Metadata\MetadataBag;
use Imagine\Image\Palette\CMYK;
use Imagine\Image\Palette\Color\CMYK as CMYKColor;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Grayscale;
use Imagine\Image\Palette\RGB;

/**
 * Imagine implementation using the Gmagick PHP extension.
 *
 * @final
 */
class Imagine extends AbstractImagine implements InfoProvider
{
    /**
     * @throws \Imagine\Exception\RuntimeException
     */
    public function __construct()
    {
        static::getDriverInfo()->checkVersionIsSupported();
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
                $gmagick = new \Gmagick($path);
                $image = $this->getClassFactory()->createImage(ClassFactoryInterface::HANDLE_GMAGICK, $gmagick, $this->createPalette($gmagick), $this->getMetadataReader()->readFile($loader));
            } else {
                $image = $this->doLoad($loader->getData(), $this->getMetadataReader()->readFile($loader));
            }
        } catch (\GmagickException $e) {
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

        $palette = $color !== null ? $color->getPalette() : new RGB();
        $color = $color !== null ? $color : $palette->color('fff');

        try {
            $gmagick = new \Gmagick();
            // Gmagick does not support creation of CMYK GmagickPixel
            // see https://bugs.php.net/bug.php?id=64466
            if ($color instanceof CMYKColor) {
                $switchPalette = $palette;
                $palette = new RGB();
                $pixel = new \GmagickPixel($palette->color((string) $color));
            } else {
                $switchPalette = null;
                $pixel = new \GmagickPixel((string) $color);
            }

            if (!$color->getPalette()->supportsAlpha() && $color->getAlpha() !== null && $color->getAlpha() < 100) {
                throw new NotSupportedException('alpha transparency is not supported');
            }

            $gmagick->newimage($width, $height, $pixel->getcolor(false));
            $gmagick->setimagecolorspace(\Gmagick::COLORSPACE_TRANSPARENT);
            $gmagick->setimagebackgroundcolor($pixel);

            $image = $this->getClassFactory()->createImage(ClassFactoryInterface::HANDLE_GMAGICK, $gmagick, $palette, new MetadataBag());

            if ($switchPalette) {
                $image->usePalette($switchPalette);
            }

            return $image;
        } catch (\GmagickException $e) {
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
        return $this->doLoad($string, $this->getMetadataReader()->readData($string));
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

        if ($content === false) {
            throw new InvalidArgumentException('Couldn\'t read given resource');
        }

        return $this->doLoad($content, $this->getMetadataReader()->readData($content, $resource));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImagineInterface::font()
     */
    public function font($file, $size, ColorInterface $color)
    {
        return $this->getClassFactory()->createFont(ClassFactoryInterface::HANDLE_GMAGICK, $file, $size, $color);
    }

    /**
     * @param \Gmagick $gmagick
     *
     * @throws \Imagine\Exception\NotSupportedException
     *
     * @return \Imagine\Image\Palette\PaletteInterface
     */
    private function createPalette(\Gmagick $gmagick)
    {
        switch ($gmagick->getimagecolorspace()) {
            case \Gmagick::COLORSPACE_SRGB:
            case \Gmagick::COLORSPACE_RGB:
                return new RGB();
            case \Gmagick::COLORSPACE_CMYK:
                return new CMYK();
            case \Gmagick::COLORSPACE_GRAY:
                return new Grayscale();
            default:
                throw new NotSupportedException('Only RGB and CMYK colorspace are currently supported');
        }
    }

    /**
     * @param string $content
     * @param \Imagine\Image\Metadata\MetadataBag $metadata
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return \Imagine\Image\ImageInterface
     */
    private function doLoad($content, MetadataBag $metadata)
    {
        try {
            $gmagick = new \Gmagick();
            $gmagick->readimageblob($content);
        } catch (\GmagickException $e) {
            throw new RuntimeException('Could not load image from string', $e->getCode(), $e);
        }

        return $this->getClassFactory()->createImage(ClassFactoryInterface::HANDLE_GMAGICK, $gmagick, $this->createPalette($gmagick), $metadata);
    }
}
