<?php

namespace Imagine\Imagick;

use Imagine\Image\ImageInterface;
use Imagine\Factory\AbstractClassFactory;
use Imagine\Image\Palette\Color\ColorInterface;

/**
 * The final implementation of Imagine\Factory\ClassFactoryInterface for Imagick.
 */
class ClassFactory extends AbstractClassFactory
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryInterface::createFont()
     */
    public function createFont($file, $size, ColorInterface $color)
    {
        return $this->finalize(new Font(new \Imagick(), $file, $size, $color));
    }
    
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryInterface::createLayers()
     */
    public function createLayers(ImageInterface $image)
    {
        return $this->finalize(new Layers($image, $image->palette(), $image->getImagick()));
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
        return $this->finalize(new \Imagine\Imagick\Effects($resource));
    }
}
