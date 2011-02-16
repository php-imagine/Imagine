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

class SizeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Imagine\Cartesian\Size::getWidth
     * @covers Imagine\Cartesian\Size::getHeight
     *
     * @dataProvider getSizes
     *
     * @param integer $width
     * @param integer $height
     */
    public function testShouldAssignWidthAndHeight($width, $height)
    {
        $size = new Size($width, $height);

        $this->assertEquals($width, $size->getWidth());
        $this->assertEquals($height, $size->getHeight());
    }

    /**
     * Data provider for testShouldAssignWidthAndHeight
     *
     * @return array
     */
    public function getSizes()
    {
        return array(
            array(1, 1),
            array(10, 10),
            array(15, 36)
        );
    }

    /**
     * @covers Imagine\Cartesian\Size::__construct
     *
     * @expectedException Imagine\Exception\InvalidArgumentException
     *
     * @dataProvider getInvalidSizes
     *
     * @param integer $width
     * @param integer $height
     */
    public function testShouldThrowExceptionOnInvalieSize($width, $height)
    {
        new Size($width, $height);
    }

    /**
     * Data provider for testShouldThrowExceptionOnInvalieSize
     *
     * @return array
     */
    public function getInvalidSizes()
    {
        return array(
            array(0, 0),
            array(15, 0),
            array(0, 25),
            array(-1, 4)
        );
    }

    /**
     * @covers Imagine\Cartesian\Size::contains
     *
     * @dataProvider getSizeBoxStartAndExpected
     *
     * @param SizeInterface       $size
     * @param SizeInterface       $box
     * @param CoordinateInterface $start
     * @param Boolean             $expected
     */
    public function testShouldDetermineIfASizeContainsABoxAtAStartPosition(
        SizeInterface       $size,
        SizeInterface       $box,
        CoordinateInterface $start,
        $expected
    ) {
        $this->assertEquals($expected, $size->contains($box, $start));
    }

    /**
     * Data provider for testShouldDetermineIfASizeContainsABoxAtAStartPosition
     *
     * @return array
     */
    public function getSizeBoxStartAndExpected()
    {
        return array(
            array(new Size(50, 50), new Size(30, 30), new Coordinate(0, 0), true),
            array(new Size(50, 50), new Size(30, 30), new Coordinate(20, 20), true),
            array(new Size(50, 50), new Size(30, 30), new Coordinate(21, 21), false),
            array(new Size(50, 50), new Size(30, 30), new Coordinate(21, 20), false),
            array(new Size(50, 50), new Size(30, 30), new Coordinate(20, 22), false),
        );
    }
}
