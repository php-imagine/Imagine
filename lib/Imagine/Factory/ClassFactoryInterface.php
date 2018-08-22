<?php

namespace Imagine\Factory;

use Imagine\Image\Palette\Color\ColorInterface;

/**
 * The interface that class factories must implement.
 */
interface ClassFactoryInterface
{
    /**
     * Create a new instance of a metadata reader.
     *
     * @return \Imagine\Image\Metadata\MetadataReaderInterface
     */
    public function createMetadataReader();

    /**
     * Create new BoxInterface instance
     *
     * @param int $width The box width
     * @param int $height The box height
     *
     * @return \Imagine\Image\BoxInterface
     */
    public function createBox($width, $height);

    /**
     * Create new FontInterface instance
     *
     * 
     *
     * @param string $file
     * @param int $size the font size in points (e.g. 10pt means 10)
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     *
     * @return \Imagine\Image\FontInterface
     */
    public function createFont($file, $size, ColorInterface $color);

    /**
     * Create a new instance of a file loader.
     *
     * @param string|mixed $path
     *
     * @return \Imagine\File\LoaderInterface
     */
    public function createFileLoader($path);

    /**
     * Crate a new instance of a layers interface
     * @param \Imagine\Image\ImageInterface $image
     * @return \Imagine\Image\LayersInterface
     */
    public function createLayers(\Imagine\Image\ImageInterface $image);

    /**
     * Create a new ImageInterface instance.
     *
     * @param mixed $resource
     * @param \Imagine\Image\Palette\PaletteInterface $palette
     * @param \Imagine\Image\Metadata\MetadataBag $metadata
     *
     * @return \Imagine\Image\ImageInterface
     */
    public function createImage($resource, \Imagine\Image\Palette\PaletteInterface $palette, \Imagine\Image\Metadata\MetadataBag $metadata);
    
    /**
     * Create a new DrawerInterface instance.
     *
     * @param mixed $resource
     *
     * @return \Imagine\Draw\DrawerInterface
     */
    public function createDrawer($resource);
    
    /**
     * Create a new EffectsInterface instance.
     *
     * @param mixed $resource
     *
     * @return \Imagine\Effects\EffectsInterface
     */
    public function createEffects($resource);    
}
