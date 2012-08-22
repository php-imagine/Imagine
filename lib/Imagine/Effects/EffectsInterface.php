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

interface EffectsInterface
{
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
     * @param $r Value of red component
     * @param $g Value of green component
     * @param $b Value of blue component
     *
     * @return EffectsInterface
     *
     * @throws RuntimeException
     */
    public function colorize($r, $g, $b);
}
