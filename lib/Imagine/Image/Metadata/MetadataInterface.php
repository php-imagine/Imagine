<?php
namespace Imagine\Image\Metadata;

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Imagine\Image\ImageInterface;

/**
 * An interface for Image Metadata
 */
interface MetadataInterface
{
    /**
     * Constants representing how the orientation of the actual scene is mapped onto the presented image,
     * inspired by Exif's Orientation tag
     * @see http://sylvana.net/jpegcrop/exif_orientation.html
     */
    const ORIENTATION_NORMAL = 1;
    const ORIENTATION_FLIPPED_HORIZONTALLY = 2;
    const ORIENTATION_ROTATED_180 = 3;
    const ORIENTATION_FLIPPED_VERTICALLY = 4;
    const ORIENTATION_ROTATED_MINUS90_FLIPPED_VERTICALLY = 5;
    const ORIENTATION_ROTATED_MINUS90 = 6;
    const ORIENTATION_ROTATED_90_FLIPPED_VERTICALLY = 7;
    const ORIENTATION_ROTATED_90 = 8;

    /**
     * @param ImageInterface $image
     */
    public function __construct(ImageInterface $image);

    /**
     * Returns an orientation integer inspired by Exif's Orientation tag
     *
     * @return integer
     */
    public function getOrientation();
}
