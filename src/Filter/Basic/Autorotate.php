<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter\Basic;

use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\ColorInterface;

/**
 * Rotates an image automatically based on exif information.
 *
 * Your attention please: This filter requires the use of the
 * ExifMetadataReader to work.
 *
 * @see https://imagine.readthedocs.org/en/latest/usage/metadata.html
 */
class Autorotate implements FilterInterface
{
    /**
     * Image transformation: flip vertically.
     *
     * @var string
     */
    const FLIP_VERTICALLY = 'V';

    /**
     * Image transformation: flip horizontally.
     *
     * @var string
     */
    const FLIP_HORIZONTALLY = 'H';

    /**
     * @var string|array|\Imagine\Image\Palette\Color\ColorInterface
     */
    private $color;

    /**
     * @param string|array|\Imagine\Image\Palette\Color\ColorInterface $color A color
     */
    public function __construct($color = '000000')
    {
        $this->color = $color;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        foreach ($this->getTransformations($image) as $transformation) {
            if ($transformation === self::FLIP_HORIZONTALLY) {
                $image->flipHorizontally();
            } elseif ($transformation === self::FLIP_VERTICALLY) {
                $image->flipVertically();
            } elseif (is_int($transformation)) {
                $image->rotate($transformation, $this->getColor($image));
            }
        }

        return $image;
    }

    /**
     * Get the transformations.
     *
     * @param \Imagine\Image\ImageInterface $image
     *
     * @return array an array containing Autorotate::FLIP_VERTICALLY, Autorotate::FLIP_HORIZONTALLY, rotation degrees
     */
    public function getTransformations(ImageInterface $image)
    {
        $transformations = array();
        $metadata = $image->metadata();
        switch (isset($metadata['ifd0.Orientation']) ? $metadata['ifd0.Orientation'] : null) {
            case 1: // top-left
                break;
            case 2: // top-right
                $transformations[] = self::FLIP_HORIZONTALLY;
                break;
            case 3: // bottom-right
                $transformations[] = 180;
                break;
            case 4: // bottom-left
                $transformations[] = self::FLIP_HORIZONTALLY;
                $transformations[] = 180;
                break;
            case 5: // left-top
                $transformations[] = self::FLIP_HORIZONTALLY;
                $transformations[] = -90;
                break;
            case 6: // right-top
                $transformations[] = 90;
                break;
            case 7: // right-bottom
                $transformations[] = self::FLIP_HORIZONTALLY;
                $transformations[] = 90;
                break;
            case 8: // left-bottom
                $transformations[] = -90;
                break;
            default: // Invalid orientation
                break;
        }

        return $transformations;
    }

    /**
     * @param \Imagine\Image\ImageInterface $image
     *
     * @return \Imagine\Image\Palette\Color\ColorInterface
     */
    private function getColor(ImageInterface $image)
    {
        if ($this->color instanceof ColorInterface) {
            return $this->color;
        }

        return $image->palette()->color($this->color);
    }
}
