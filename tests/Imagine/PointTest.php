<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine;

class PointTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider getCoordinates
	 */
    public function testShouldAssignXYCoordinates($x, $y)
    {
        $point = new Point($x, $y);

        $this->assertEquals($x, $point->getX());
        $this->assertEquals($y, $point->getY());
    }

    public function getCoordinates()
    {
        return array(
            array(0, 0),
            array(5, 15),
            array(10, 23),
            array(42, 30),
            array(81, 16),
        );
    }
}
