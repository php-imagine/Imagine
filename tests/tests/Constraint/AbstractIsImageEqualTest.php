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
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
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
        $this->assertRegExp('/^Argument #1 .* must be an? /', $error->getMessage());

        try {
            $this->assertImageEquals($image, 'invalid 2');
            $error = null;
        } catch (\Exception $x) {
            $error = $x;
        }
        $this->assertInstanceOf('PHPUnit\Framework\Exception', $error, 'the second argument should be an ImageInterface instance');
        $this->assertRegExp('/^Argument #1 .* must be an? /', $error->getMessage());

        try {
            $this->assertImageEquals($image, $image, '', 'invalid');
            $error = null;
        } catch (\Exception $x) {
            $error = $x;
        }
        $this->assertInstanceOf('PHPUnit\Framework\Exception', $error, 'the third argument should be a number');
        $this->assertRegExp('/^Argument #2 .* must be an? /', $error->getMessage());

        try {
            $this->assertImageEquals($image, $image, '', 0.1, null, 'invalid');
            $error = null;
        } catch (\Exception $x) {
            $error = $x;
        }
        $this->assertInstanceOf('PHPUnit\Framework\Exception', $error, 'the fourth argument should be an integer');
        $this->assertRegExp('/^Argument #4 .* must be an? /', $error->getMessage());

        try {
            $this->assertImageEquals($image, $image, '', 0.1, null, 0);
            $error = null;
        } catch (\Exception $x) {
            $error = $x;
        }
        $this->assertInstanceOf('PHPUnit\Framework\Exception', $error, 'the fourth argument should be a positive integer');
        $this->assertRegExp('/^Argument #4 .* must be an? /', $error->getMessage());

        try {
            $this->assertImageEquals('foo', 'bar', '', 0, $this->getImagine());
            $error = null;
        } catch (\Exception $x) {
            $error = $x;
        }
        $this->assertInstanceOf('Imagine\Exception\InvalidArgumentException', $error, 'the fourth argument should be a positive integer');
        $this->assertRegExp('/^File .* does not exist.$/', $error->getMessage());
    }

    public function sameImagesProvider()
    {
        $imagine = $this->getImagine();
        $palette = new RGB();

        return array(
            array(
                $imagine->create(new Box(3, 3), $palette->color('#000')),
                $imagine->create(new Box(3, 3), $palette->color('#000')),
            ),
        );
    }

    /**
     * @dataProvider sameImagesProvider
     *
     * @param \Imagine\Image\ImageInterface $image1
     * @param \Imagine\Image\ImageInterface $image2
     */
    public function testSameImages(ImageInterface $image1, ImageInterface $image2)
    {
        $this->assertImageEquals($image1, $image2);
    }

    public function differentImagesProvider()
    {
        $imagine = $this->getImagine();
        $palette = new RGB();

        return array(
            array(
                $imagine->create(new Box(3, 3), $palette->color('#000')),
                $imagine->create(new Box(3, 3), $palette->color('#fff')),
            ),
        );
    }

    /**
     * @dataProvider differentImagesProvider
     *
     * @param \Imagine\Image\ImageInterface $image1
     * @param \Imagine\Image\ImageInterface $image2
     */
    public function testDifferentImages(ImageInterface $image1, ImageInterface $image2)
    {
        try {
            $this->assertImageEquals($image1, $image2);
            $error = null;
        } catch (\Exception $x) {
            $error = $x;
        }
        $this->assertInstanceOf('PHPUnit\Framework\ExpectationFailedException', $error, 'different images should be detected as different');
    }

    /**
     * @return \Imagine\Image\ImagineInterface
     */
    abstract protected function getImagine();
}
