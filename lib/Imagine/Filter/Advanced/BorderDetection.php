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
 * BorderDetection based on Laplace-Operator. Three different variants are offered:.
 * <code><pre>
 *   First          Second            Third
 *  0,  1, 0       1,  1, 1,       -1,  2, -1,
 *  1, -4, 1  and  1, -8, 1,  and   2, -4,  2,
 *  0,  1, 0       1,  1, 1        -1,  2, -1
 * </pre></code>.
 *
 * Consider to apply this filter on a grayscaled image.
 */
class BorderDetection extends Neighborhood implements FilterInterface
{
    /**
     * First variant of the detection matrix.
     *
     * @var int
     */
    const VARIANT_ONE = 0;

    /**
     * Second variant of the detection matrix.
     *
     * @var int
     */
    const VARIANT_TWO = 1;

    /**
     * Third variant of the detection matrix.
     *
     * @var int
     */
    const VARIANT_THREE = 2;

    /**
     * Initialize this filter.
     *
     * @param int $variant One of the BorderDetection::VARIANT_... constants.
     *
     * @throws \Imagine\Exception\InvalidArgumentException throws an InvalidArgumentException if $variant is not valid
     */
    public function __construct($variant = self::VARIANT_ONE)
    {
        $matrix = null;

        switch ($variant) {
            case self::VARIANT_ONE:
                $matrix = new Matrix(3, 3, array(
                    0,  1, 0,
                    1, -4, 1,
                    0,  1, 0,
                ));
                break;
            case self::VARIANT_TWO:
                $matrix = new Matrix(3, 3, array(
                    1,  1, 1,
                    1, -8, 1,
                    1,  1, 1,
                ));
                break;
            case self::VARIANT_THREE:
                $matrix = new Matrix(3, 3, array(
                    -1,  2, -1,
                    2, -4,  2,
                    -1,  2, -1,
                ));
                break;
            default:
                throw new InvalidArgumentException('Unknown variant ' . $variant);
        }

        parent::__construct($matrix);
    }
}
