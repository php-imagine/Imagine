<?php
/*
 * This file is part of the Imagine package.
 *
 * (c) Amri Sannang <amri.sannang@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Image\Effects;

use Imagine\Image\Effects\ConvolutionMatrix;
use Imagine\Test\ImagineTestCase;

class ConvolutionMatrixTest extends ImagineTestCase
{
    public function testNormalize()
    {
        $matrix = new ConvolutionMatrix(
            -1, -1, -1,
            -1, 16, -1,
            -1, -1, -1
        );
        $normalizedMatrix = $matrix->normalize();
        $this->assertEquals(
            array(-.125, -.125, -.125, -.125, 2, -.125, -.125, -.125, -.125),
            $normalizedMatrix->getKernel()
        );
        $this->assertEquals(
            array(
                array(-.125, -.125, -.125),
                array(-.125, 2, -.125),
                array(-.125, -.125, -.125),
            ),
            $normalizedMatrix->getMatrix()
        );
        $this->assertEquals(
            array(-1, -1, -1, -1, 16, -1, -1, -1, -1),
            $matrix->getKernel()
        );
        $this->assertEquals(
            array(
                array(-1, -1, -1),
                array(-1, 16, -1),
                array(-1, -1, -1),
            ),
            $matrix->getMatrix()
        );
    }
}
