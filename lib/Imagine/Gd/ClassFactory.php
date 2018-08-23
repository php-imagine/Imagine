<?php

namespace Imagine\Gd;

use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Factory\AbstractClassFactory;
use Imagine\Exception\RuntimeException;

/**
* The final implementation of Imagine\Factory\ClassFactoryInterface for GD.
 */
class ClassFactory extends AbstractClassFactory
{
    /**
     * @var array|null
     */
    private static $gdInfo;

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryInterface::createFont()
     */
    public function createFont($file, $size, ColorInterface $color)
    {
        $gdInfo = static::getGDInfo();
        if (!$gdInfo['FreeType Support']) {
            throw new RuntimeException('GD is not compiled with FreeType support');
        }

        return $this->finalize(new Font($file, $size, $color));
    }
    
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryInterface::createLayers()
     */
    public function createLayers(ImageInterface $image)
    {
        return $this->finalize(new Layers($image, $image->palette(), $image->getGdResource()));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryInterface::createImage()
     */
    public function createImage($resource, \Imagine\Image\Palette\PaletteInterface $palette, \Imagine\Image\Metadata\MetadataBag $metadata)
    {
        return $this->finalize(new Image($resource, $palette, $metadata));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryInterface::createDrawer()
     */
    public function createDrawer($resource)
    {
        return $this->finalize(new Drawer($resource));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryInterface::createEffects()
     */
    public function createEffects($resource)
    {
        return $this->finalize(new Effects($resource));
    }

    /**
     * @return array
     */
    protected static function getGDInfo()
    {
        if (self::$gdInfo === null) {
            if (!function_exists('gd_info')) {
                throw new RuntimeException('Gd not installed');
            }
            self::$gdInfo = gd_info();
        }

        return self::$gdInfo;
    }
}
