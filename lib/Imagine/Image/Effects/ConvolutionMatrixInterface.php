<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Amri Sannang <amri.sannang@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image\Effects;

/**
 * The convolution kernel interface
 */
interface ConvolutionMatrixInterface
{
    /**
     * Normalizes this convolution kernel. Depending on implementation, may
     * return a copy instead of modifying this object.
     *
     * @return ConvolutionMatrixInterface
     */
    public function normalize();

    /**
     * Returns convolution as a matrix (array of array of floats)
     *
     * @return array|array[]
     */
    public function getMatrix();

    /**
     * Returns convolution as a kernel.
     *
     * @return array|float[]
     */
    public function getKernel();
}
