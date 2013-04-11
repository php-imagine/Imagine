<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Effects;

use Imagine\Exception\RuntimeException;
use Imagine\Image\Color;

/**
 * Interface for the effects
 */
interface EffectsInterface
{
    /**
     * New effects object create factory
     *
     * @param  mixed            $resource
     * @return EffectsInterface
     */
    public static function create($resource);

    /**
     * Apply gamma correction
     *
     * @param  float            $correction
     * @return EffectsInterface
     *
     * @throws RuntimeException
     */
    public function gamma($correction);

    /**
     * Invert the colors of the image
     *
     * @return EffectsInterface
     *
     * @throws RuntimeException
     */
    public function negative();

    /**
     * Grayscale the image
     *
     * @return EffectsInterface
     *
     * @throws RuntimeException
     */
    public function grayscale();

    /**
     * Colorize the image
     *
     * @param Color             $color
     *
     * @return EffectsInterface
     *
     * @throws RuntimeException
     */
    public function colorize(Color $color);

    /**
     * Sharpens the image
     *
     * @return EffectsInterface
     *
     * @throws RuntimeException
     */
    public function sharpen();
}
