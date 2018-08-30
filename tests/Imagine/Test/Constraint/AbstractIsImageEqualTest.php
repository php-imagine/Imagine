<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Constraint;

use Imagine\Image\Box;
use Imagine\Test\ImagineTestCase;

abstract class AbstractIsImageEqualTest extends ImagineTestCase
{
    public function testThrowsExceptionWithInvalidArguments()
    {
        $image = $this->getImagine()->create(new Box(1, 1));
        try {
            $this->assertImageEquals('invalid 1', $image);
            $error = null;
        } catch (\Exception $x) {
            $error = $x;
        }
        $this->assertInstanceOf('PHPUnit\Framework\Exception', $error, 'the first argument should be an ImageInterface instance');

        try {
            $this->assertImageEquals($image, 'invalid 2');
            $error = null;
        } catch (\Exception $x) {
            $error = $x;
        }
        $this->assertInstanceOf('PHPUnit\Framework\Exception', $error, 'the second argument should be an ImageInterface instance');

        try {
            $this->assertImageEquals($image, $image, '', 'invalid');
            $error = null;
        } catch (\Exception $x) {
            $error = $x;
        }
        $this->assertInstanceOf('PHPUnit\Framework\Exception', $error, 'the third argument should be a number');

        try {
            $this->assertImageEquals($image, $image, '', 0.1, 'invalid');
            $error = null;
        } catch (\Exception $x) {
            $error = $x;
        }
        $this->assertInstanceOf('PHPUnit\Framework\Exception', $error, 'the fourth argument should be an integer');

        try {
            $this->assertImageEquals($image, $image, '', 0.1, 0);
            $error = null;
        } catch (\Exception $x) {
            $error = $x;
        }
        $this->assertInstanceOf('PHPUnit\Framework\Exception', $error, 'the fourth argument should be a positive integer');
    }

    /**
     * @return \Imagine\Image\ImagineInterface
     */
    abstract protected function getImagine();
}
