<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter\Advanced;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Filter\FilterInterface;
use Imagine\Utils\Matrix;

/**
 * BorderDetection based on Laplace-Operator. Three different variants are offered:
 *
 *               First          Second            Third
 *              0,  1, 0       1,  1, 1,       -1,  2, -1,
 *              1, -4, 1  and  1, -8, 1,  and   2, -4,  2,
 *              0,  1, 0       1,  1, 1        -1,  2, -1
 *
 * Consider to apply this filter on a grayscaled image.
 */
class BorderDetection extends Neighborhood implements FilterInterface
{
    const VARIANT_ONE   = 0;
    const VARIANT_TWO   = 1;
    const VARIANT_THREE = 2;

    public function __construct($variant = self::VARIANT_ONE)
    {
        $matrix = null;

        if (self::VARIANT_ONE === $variant) {
            $matrix = new Matrix(3, 3, array(
                0,  1, 0,
                1, -4, 1,
                0,  1, 0
            ));
        }

        if (self::VARIANT_TWO === $variant) {
            $matrix = new Matrix(3, 3, array(
                1,  1, 1,
                1, -8, 1,
                1,  1, 1
            ));
        }

        if (self::VARIANT_THREE === $variant) {
            $matrix = new Matrix(3, 3, array(
                -1,  2, -1,
                2, -4,  2,
                -1,  2, -1
            ));
        }

        if (null === $matrix) {
            throw new InvalidArgumentException('Variant ' . $variant . ' unknown');
        }

        parent::__construct($matrix);
    }
}