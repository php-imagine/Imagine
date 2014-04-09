<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Utils;

use Imagine\Utils\Matrix;

class MatrixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderForTestMatrixHasAtLeastOneElement
     */
    public function testMatrixHasAtLeastOneElement($width, $height, $exceptionMessage)
    {
        if (null !== $exceptionMessage) {
            $this->setExpectedException('Imagine\Exception\InvalidArgumentException', $exceptionMessage);
        }

        new Matrix($width, $height);
    }

    public function dataProviderForTestMatrixHasAtLeastOneElement()
    {
        return array(
            array(0, 1, 'width has to be > 0'),
            array(1, 0, 'height has to be > 0'),
            array(1, 1, null)
        );
    }

    public function testMatrixComplainsIfYouGiveToMuchElements()
    {
        $this->setExpectedException(
            'Imagine\Exception\InvalidArgumentException',
            'there are more provided elements than space in the matrix'
        );

        new Matrix(1, 1, array(1,1));
    }

    public function testElementsGetCorrectlyArrangedAndFilledUp()
    {
        $matrix = new Matrix(3, 3, array(1, 2, 3, 4, 5, 6));

        $this->assertEquals(1, $matrix->getElementAt(0, 0));
        $this->assertEquals(2, $matrix->getElementAt(1, 0));
        $this->assertEquals(3, $matrix->getElementAt(2, 0));
        $this->assertEquals(4, $matrix->getElementAt(0, 1));
        $this->assertEquals(5, $matrix->getElementAt(1, 1));
        $this->assertEquals(6, $matrix->getElementAt(2, 1));

        $this->assertEquals(0, $matrix->getElementAt(0, 2));
        $this->assertEquals(0, $matrix->getElementAt(1, 2));
        $this->assertEquals(0, $matrix->getElementAt(2, 2));
    }

    /**
     * @dataProvider dataProviderForTestMatrixCorrectlyGivesOutOfBoundExceptions
     */
    public function testMatrixCorrectlyGivesOutOfBoundExceptions($x, $y, $exceptionMessage)
    {
        $this->setExpectedException('Imagine\Exception\OutOfBoundsException', $exceptionMessage);

        $matrix = new Matrix(1, 1);

        $matrix->getElementAt($x, $y);
    }

    public function dataProviderForTestMatrixCorrectlyGivesOutOfBoundExceptions()
    {
        return array(
            array(0,  -1, 'There is no position (0, -1) in this matrix'),
            array(-1,  0, 'There is no position (-1, 0) in this matrix'),
            array(-1, -1, 'There is no position (-1, -1) in this matrix'),
            array(0,   1, 'There is no position (0, 1) in this matrix'),
            array( 1,  0, 'There is no position (1, 0) in this matrix'),
            array( 1,  1, 'There is no position (1, 1) in this matrix'),
        );
    }
}