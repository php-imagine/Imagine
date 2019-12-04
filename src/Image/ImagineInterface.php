<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

use Imagine\Factory\ClassFactoryAwareInterface;
use Imagine\Image\Metadata\MetadataReaderInterface;
use Imagine\Image\Palette\Color\ColorInterface;

/**
 * The imagine interface.
 */
interface ImagineInterface extends ClassFactoryAwareInterface
{
    const VERSION = '1.2.3';

    /**
     * Creates a new empty image with an optional background color.
     *
     * @param \Imagine\Image\BoxInterface $size
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return \Imagine\Image\ImageInterface
     */
    public function create(BoxInterface $size, ColorInterface $color = null);

    /**
     * Opens an existing image from $path.
     *
     * @param string|\Imagine\File\LoaderInterface|mixed $path the file path, a LoaderInterface instance, or an object whose string representation is the image path
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return \Imagine\Image\ImageInterface
     */
    public function open($path);

    /**
     * Loads an image from a binary $string.
     *
     * @param string $string
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return \Imagine\Image\ImageInterface
     */
    public function load($string);

    /**
     * Loads an image from a resource $resource.
     *
     * @param resource $resource
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return \Imagine\Image\ImageInterface
     */
    public function read($resource);

    /**
     * Constructs a font with specified $file, $size and $color.
     *
     * The font size is to be specified in points (e.g. 10pt means 10)
     *
     * @param string $file
     * @param int $size
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     *
     * @return \Imagine\Image\FontInterface
     */
    public function font($file, $size, ColorInterface $color);

    /**
     * Set the object to be used to read image metadata.
     *
     * @param \Imagine\Image\Metadata\MetadataReaderInterface $metadataReader
     *
     * @return $this
     */
    public function setMetadataReader(MetadataReaderInterface $metadataReader);

    /**
     * Get the object to be used to read image metadata.
     *
     * @return \Imagine\Image\Metadata\MetadataReaderInterface
     */
    public function getMetadataReader();
}
