<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

class PointTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @covers Imagine\Image\Point::getX
	 * @covers Imagine\Image\Point::getY
	 * @covers Imagine\Image\Point::in
	 *
	 * @dataProvider getCoordinates
	 *
	 * @param integer       $x
	 * @param integer       $y
	 * @param BoxInterface $box
	 * @param Boolean       $expected
	 */
    public function testShouldAssignXYCoordinates($x, $y, BoxInterface $box, $expected)
    {
        $coordinate = new Point($x, $y);

        $this->assertEquals($x, $coordinate->getX());
        $this->assertEquals($y, $coordinate->getY());

        $this->assertEquals($expected, $coordinate->in($box));
    }

    /**
     * Data provider for testShouldAssignXYCoordinates
     *
     * @return array
     */
    public function getCoordinates()
    {
        return array(
            array(0, 0, new Box(5, 5), true),
            array(5, 15, new Box(5, 5), false),
            array(10, 23, new Box(10, 10), false),
            array(42, 30, new Box(50, 50), true),
            array(81, 16, new Box(50, 10), false),
        );
    }

    /**
     * @covers Imagine\Image\Point::__construct
     *
     * @expectedException Imagine\Exception\InvalidArgumentException
     *
     * @dataProvider getInvalidCoordinates
     *
     * @param integer $x
     * @param integer $y
     */
    public function testShouldThrowExceptionOnInvalidCoordinates($x, $y)
    {
        new Point($x, $y);
    }

    /**
     * Data provider for testShouldThrowExceptionOnInvalidCoordinates
     *
     * @return array
     */
    public function getInvalidCoordinates()
    {
        return array(
            array(-1, 0),
            array(0, -1)
        );
    }

    /**
     * @covers Imagine\Image\Point::__toString
     */
    public function testToString()
    {
        $this->assertEquals('(50, 50)', (string) new Point(50, 50));
    }
}
