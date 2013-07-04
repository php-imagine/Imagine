<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Image\Fill\Gradient;

use Imagine\Image\Fill\Gradient\Linear;
use Imagine\Image\Color;
use Imagine\Image\PointInterface;

abstract class LinearTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Imagine\Image\Fill\FillInterface
     */
    private $fill;

    /**
     * @var Imagine\Image\Color
     */
    private $start;

    /**
     * @var Imagine\Image\Color
     */
    private $end;

    protected function setUp()
    {
        $this->start = $this->getStart();
        $this->end   = $this->getEnd();
        $this->fill  = $this->getFill($this->start, $this->end);
    }

    /**
     * @dataProvider getPointsAndColors
     *
     * @param integer                      $shade
     * @param Imagine\Image\PointInterface $position
     */
    public function testShouldProvideCorrectColorsValues(Color $color, PointInterface $position)
    {
        $this->assertEquals($color, $this->fill->getColor($position));
    }

    /**
     * @covers Imagine\Image\Fill\Gradient\Linear::getStart
     * @covers Imagine\Image\Fill\Gradient\Linear::getEnd
     */
    public function testShouldReturnCorrectStartAndEnd()
    {
        $this->assertSame($this->start, $this->fill->getStart());
        $this->assertSame($this->end, $this->fill->getEnd());
    }

    /**
     * @param Imagine\Image\Color $start
     * @param Imagine\Image\Color $end
     *
     * @return Imagine\Image\Fill\FillInterface
     */
    abstract protected function getFill(Color $start, Color $end);

    /**
     * @return Imagine\Image\Color
     */
    abstract protected function getStart();

    /**
     * @return Imagine\Image\Color
     */
    abstract protected function getEnd();

    /**
     * @return array
     */
    abstract public function getPointsAndColors();
}
