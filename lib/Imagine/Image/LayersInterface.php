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

use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;

/**
 * The layers interface.
 */
interface LayersInterface extends \Iterator, \Countable, \ArrayAccess
{
    /**
     * Merge layers into the original objects.
     *
     * @throws RuntimeException
     */
    public function merge();

    /**
     * Animates layers.
     *
     * @param string $format The output output format
     * @param int $delay The delay in milliseconds between two frames
     * @param int $loops The number of loops, 0 means infinite
     *
     * @throws InvalidArgumentException In case an invalid argument is provided
     * @throws RuntimeException In case the driver fails to animate
     *
     * @return LayersInterface
     */
    public function animate($format, $delay, $loops);

    /**
     * Coalesce layers. Each layer in the sequence is the same size as the first and composited with the next layer in
     * the sequence.
     */
    public function coalesce();

    /**
     * Adds an image at the end of the layers stack.
     *
     * @param ImageInterface $image
     *
     * @throws RuntimeException
     *
     * @return LayersInterface
     */
    public function add(ImageInterface $image);

    /**
     * Set an image at offset.
     *
     * @param int $offset
     * @param ImageInterface $image
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     *
     * @return LayersInterface
     */
    public function set($offset, ImageInterface $image);

    /**
     * Removes the image at offset.
     *
     * @param int $offset
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     *
     * @return LayersInterface
     */
    public function remove($offset);

    /**
     * Returns the image at offset.
     *
     * @param int $offset
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     *
     * @return ImageInterface
     */
    public function get($offset);

    /**
     * Returns true if a layer at offset is preset.
     *
     * @param int $offset
     *
     * @return bool
     */
    public function has($offset);
}
