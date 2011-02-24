<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Mask\Gradient;

use Imagine\Point;

class VerticalTest extends LinearTest
{
    /**
     * (non-PHPdoc)
     * @see Imagine\Mask\Gradient.LinearTest::getMask()
     */
    protected function getMask()
    {
        return new Vertical(100);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Mask\Gradient.LinearTest::getPointsAndShades()
     */
    public function getPointsAndShades()
    {
        return array(
            array(255, new Point(5, 100)),
            array(0, new Point(15, 0)),
            array(128, new Point(25, 50)),
        );
    }
}
