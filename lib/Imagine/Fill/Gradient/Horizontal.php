<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Fill\Gradient;

use Imagine\Image\PointInterface;

final class Horizontal extends Linear
{
    /**
     * (non-PHPdoc)
     * @see Imagine\Mask\Gradient\Linear::getDistance()
     */
    public function getDistance(PointInterface $position)
    {
        return $position->getX();
    }
}
