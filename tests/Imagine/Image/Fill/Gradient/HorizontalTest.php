<?php
/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Imagine\Image\Fill\Gradient;

use Imagine\Image\Color;
use Imagine\Image\Point;

class HorizontalTest extends LinearTest
{
    /**
     * (non-PHPdoc)
     * @see Imagine\Image\Fill\Gradient\LinearTest::getEnd()
     */
    protected function getEnd()
    {
        return new Color('fff');
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\Fill\Gradient\LinearTest::getStart()
     */
    protected function getStart()
    {
        return new Color('000');
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\Fill\Gradient\LinearTest::getMask()
     */
    protected function getFill(Color $start, Color $end)
    {
        return new Horizontal(100, $start, $end);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\Fill\Gradient\LinearTest::getPointsAndShades()
     */
    public function getPointsAndColors()
    {
        return array(array(new Color('fff'), new Point(100, 5)),
        array(new Color('000'), new Point(0, 15)),
        array(new Color(array(128, 128, 128)), new Point(50, 25)));
    }
}
