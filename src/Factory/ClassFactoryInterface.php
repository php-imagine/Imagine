<?php

namespace Imagine\Factory;

use Imagine\Image\Palette\Color\ColorInterface;

/**
 * The interface that class factories must implement.
 */
interface ClassFactoryInterface
{
    /**
     * The handle to be used for the GD manipulation library.
     *
     * @var string
     */
    const HANDLE_GD = 'gd';

    /**
     * The handle to be used for the Gmagick manipulation library.
     *
     * @var string
     */
    const HANDLE_GMAGICK = 'gmagick';

    /**
     * The handle to be used for the Imagick manipulation library.
     *
     * @var string
     */
    const HANDLE_IMAGICK = 'imagick';

    /**
     * Create a new instance of a metadata reader.
     *
     * @return \Imagine\Image\Metadata\MetadataReaderInterface
     */
    public function createMetadataReader();

    /**
     * Create new BoxInterface instance.
     *
     * @param int $width The box width
     * @param int $height The box height
     *
     * @return \Imagine\Image\BoxInterface
     */
    public function createBox($width, $height);

    /**
     * Create new FontInterface instance.
     *
     * @param string $handle The handle that identifies the manipulation library (one of the HANDLE_... constants, or your own implementation).
     * @param string $file
     * @param int $size the font size in points (e.g. 10pt means 10)
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     *
     * @return \Imagine\Image\FontInterface
     */
    public function createFont($handle, $file, $size, ColorInterface $color);

    /**
     * Create a new instance of a file loader.
     *
     * @param string|mixed $path
     *
     * @return \Imagine\File\LoaderInterface
     */
    public function createFileLoader($path);

    /**
     * Crate a new instance of a layers interface.
     *
     * @param string $handle The handle that identifies the manipulation library (one of the HANDLE_... constants, or your own implementation).
     * @param \Imagine\Image\ImageInterface $image
     * @param mixed|null $initialKey the key of the initially selected layer
     *
     * @return \Imagine\Image\LayersInterface
     */
    public function createLayers($handle, \Imagine\Image\ImageInterface $image, $initialKey = null);

    /**
     * Create a new ImageInterface instance.
     *
     * @param string $handle The handle that identifies the manipulation library (one of the HANDLE_... constants, or your own implementation).
     * @param mixed $resource
     * @param \Imagine\Image\Palette\PaletteInterface $palette
     * @param \Imagine\Image\Metadata\MetadataBag $metadata
     *
     * @return \Imagine\Image\ImageInterface
     */
    public function createImage($handle, $resource, \Imagine\Image\Palette\PaletteInterface $palette, \Imagine\Image\Metadata\MetadataBag $metadata);

    /**
     * Create a new DrawerInterface instance.
     *
     * @param string $handle The handle that identifies the manipulation library (one of the HANDLE_... constants, or your own implementation).
     * @param mixed $resource
     *
     * @return \Imagine\Draw\DrawerInterface
     */
    public function createDrawer($handle, $resource);

    /**
     * Create a new EffectsInterface instance.
     *
     * @param string $handle The handle that identifies the manipulation library (one of the HANDLE_... constants, or your own implementation).
     * @param mixed $resource
     *
     * @return \Imagine\Effects\EffectsInterface
     */
    public function createEffects($handle, $resource);
}
