<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Filter\Advanced;


use Imagine\Test\Filter\FilterTestCase;
use Imagine\Filter\Advanced\CorrectExifRotation;

class CorrectExifRotationTest extends FilterTestCase
{

    public function testCorrectExifRotationWith90Deg()
    {

         $exifData = array('Orientation' => 6);
         $image       = $this->getImage();

         $image->expects($this->once())
               ->method('rotate')
               ->with(90);

         $filter = new CorrectExifRotation($exifData);
         $this->assertSame($image, $filter->apply($image));

    }

    public function testCorrectExifRotationWith180Deg()
    {

        $exifData = array('Orientation' => 3);
        $image       = $this->getImage();

        $image->expects($this->once())
        ->method('rotate')
        ->with(180);

        $filter = new CorrectExifRotation($exifData);
        $this->assertSame($image, $filter->apply($image));

    }

    public function testCorrectExifRotationWithMinus90Deg()
    {

        $exifData = array('Orientation' => 8);
        $image       = $this->getImage();

        $image->expects($this->once())
        ->method('rotate')
        ->with(-90);

        $filter = new CorrectExifRotation($exifData);
        $this->assertSame($image, $filter->apply($image));

    }

    public function testCorrectExifRotationWithUnknownValue()
    {

        $exifData = array('Orientation' => 'invalid or unknown value');
        $image       = $this->getImage();

        $image->expects($this->once())
        ->method('rotate')
        ->with(0);

        $filter = new CorrectExifRotation($exifData);
        $this->assertSame($image, $filter->apply($image));

    }

    public function testCorrectExifRotationWithMissingOrientation()
    {

        $exifData = array('someOtherKey' => 'someValue');
        $image       = $this->getImage();

        $image->expects($this->never())
        ->method('rotate');

        $filter = new CorrectExifRotation($exifData);
        $this->assertSame($image, $filter->apply($image));

    }

    public function testCorrectExifRotationWith90DegAndColor()
    {

        $exifData = array('Orientation' => 6);
        $image = $this->getImage();
        $color = $this->getColor();

         $image->expects($this->once())
               ->method('rotate')
               ->with(90, $color);

        $filter = new CorrectExifRotation($exifData, $color);
        $this->assertSame($image, $filter->apply($image));

    }
}
