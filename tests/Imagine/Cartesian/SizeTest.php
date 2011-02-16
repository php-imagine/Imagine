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
     * @dataProvider getSizes
     */
    public function testShouldAssignWidthAndHeight($width, $height)
    {
        $size = new Size($width, $height);

        $this->assertEquals($width, $size->getWidth());
        $this->assertEquals($height, $size->getHeight());
    }

    public function getSizes()
    {
        return array(
            array(1, 1),
            array(10, 10),
            array(15, 36)
        );
    }

    /**
     * @expectedException Imagine\Exception\InvalidArgumentException
     *
     * @dataProvider getInvalidSizes
     */
    public function testShouldThrowExceptionOnInvalieSize($width, $height)
    {
        new Size($width, $height);
    }

    public function getInvalidSizes()
    {
        return array(
            array(0, 0),
            array(15, 0),
            array(0, 25),
            array(-1, 4)
        );
    }
}
