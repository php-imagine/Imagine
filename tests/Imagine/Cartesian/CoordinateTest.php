<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Cartesian;

class CoordinateTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @covers Imagine\Cartesian\Coordinate::getX
	 * @covers Imagine\Cartesian\Coordinate::getY
	 * @covers Imagine\Cartesian\Coordinate::in
	 *
	 * @dataProvider getCoordinates
	 *
	 * @param integer       $x
	 * @param integer       $y
	 * @param SizeInterface $box
	 * @param Boolean       $expected
	 */
    public function testShouldAssignXYCoordinates($x, $y, SizeInterface $box, $expected)
    {
        $coordinate = new Coordinate($x, $y);

        $this->assertEquals($x, $coordinate->getX());
        $this->assertEquals($y, $coordinate->getY());

        $this->assertEquals($expected, $coordinate->in($box));
    }

    /**
     * Data provider for testShouldAssignXYCoordinates
     */
    public function getCoordinates()
    {
        return array(
            array(0, 0, new Size(5, 5), true),
            array(5, 15, new Size(5, 5), false),
            array(10, 23, new Size(10, 10), false),
            array(42, 30, new Size(50, 50), true),
            array(81, 16, new Size(50, 10), false),
        );
    }
}
