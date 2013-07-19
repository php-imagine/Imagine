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

use Imagine\Image\Palette\RGB;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\PointInterface;

abstract class LinearTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Imagine\Image\Fill\FillInterface
     */
    private $fill;

    /**
     * @var ColorInterface
     */
    private $start;

    /**
     * @var ColorInterface
     */
    private $end;
    protected $palette;

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
    public function testShouldProvideCorrectColorsValues(ColorInterface $color, PointInterface $position)
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

    protected function getColor($color)
    {
        static $palette;

        if (!$palette) {
            $palette = new RGB();
        }

        return $palette->color($color);
    }

    /**
     * @param ColorInterface $start
     * @param ColorInterface $end
     *
     * @return Imagine\Image\Fill\FillInterface
     */
    abstract protected function getFill(ColorInterface $start, ColorInterface $end);

    /**
     * @return ColorInterface
     */
    abstract protected function getStart();

    /**
     * @return ColorInterface
     */
    abstract protected function getEnd();

    /**
     * @return array
     */
    abstract public function getPointsAndColors();
}
