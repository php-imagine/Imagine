<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Mask;

use Imagine\PointInterface;

interface MaskInterface
{
    /**
     * Gets the shade value for the given position
     *
     * Shade is always between 0 and 255
     *
     * @param Imagine\PointInterface $position
     *
     * @return integer
     */
    function getShade(PointInterface $position);
}
