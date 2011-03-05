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

use Imagine\Image\Color;
use Imagine\Image\PointInterface;

abstract class LinearTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Imagine\Fill\FillInterface
     */
    private $fill;

    protected function setUp()
    {
        $this->fill = $this->getFill();
    }

    /**
     * @dataProvider getPointsAndColors
     *
     * @param integer                $shade
     * @param Imagine\Image\PointInterface $position
     */
    public function testShouldProvideCorrectColorsValues(Color $color, PointInterface $position)
    {
        $this->assertEquals($color, $this->fill->getColor($position));
    }

    /**
     * @return Imagine\Fill\FillInterface
     */
    abstract protected function getFill();

    /**
     * @return array
     */
    abstract public function getPointsAndColors();
}
