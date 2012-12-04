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

use Imagine\Draw\DrawerInterface;
use Imagine\Effects\EffectsInterface;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\PointInterface;
use Imagine\Exception\RuntimeException;
use Imagine\Exception\OutOfBoundsException;

/**
 * The image interface
 */
interface ImageInterface extends ManipulatorInterface
{
    const RESOLUTION_PIXELSPERINCH = 'ppi';
    const RESOLUTION_PIXELSPERCENTIMETER = 'ppc';

    /**
     * Returns the image content as a binary string
     *
     * @param string $format
     * @param array  $options
     *
     * @throws RuntimeException
     *
     * @return string binary
     */
    public function get($format, array $options = array());

    /**
     * Returns the image content as a PNG binary string
     *
     * @throws RuntimeException
     *
     * @return string binary
     */
    public function __toString();

    /**
     * Instantiates and returns a DrawerInterface instance for image drawing
     *
     * @return DrawerInterface
     */
    public function draw();

    /**
     * @return EffectsInterface
     */
    public function effects();

    /**
     * Returns current image size
     *
     * @return BoxInterface
     */
    public function getSize();

    /**
     * Transforms creates a grayscale mask from current image, returns a new
     * image, while keeping the existing image unmodified
     *
     * @return ImageInterface
     */
    public function mask();

    /**
     * Returns array of image colors as Imagine\Image\Color instances
     *
     * @return array
     */
    public function histogram();

    /**
     * Returns color at specified positions of current image
     *
     * @param PointInterface $point
     *
     * @throws RuntimeException
     *
     * @return Color
     */
    public function getColorAt(PointInterface $point);

    /**
     * Returns the image layers when applicable.
     *
     * @throws RuntimeException     In case the layer can not be returned
     * @throws OutOfBoundsException In case the index is not a valid value
     *
     * @return LayersInterface
     */
    public function layers();
}
