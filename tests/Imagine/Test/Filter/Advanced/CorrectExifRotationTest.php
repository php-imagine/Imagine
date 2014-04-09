<?php

/**
 *
 * @author Markus Nietsloh
 */
namespace Imagine\Test\Filter\Advanced;

use Imagine\Test\Filter\FilterTestCase;
use Imagine\Filter\Advanced\CorrectExifRotation;

class CorrectExifRotationTest extends FilterTestCase
{

    public function testCorrectExifRotationWith90Deg()
    {
         $imageStream = $this->getImageStreamForRotation(90);
         $image       = $this->getImage();

         $image->expects($this->once())
               ->method('rotate')
               ->with(90);

         $image->expects($this->once())
         ->method('get')
         ->will($this->returnValue($imageStream));

         $filter = new CorrectExifRotation();
         $this->assertSame($image, $filter->apply($image));
    }

    public function testCorrectExifRotationWith180Deg()
    {
        $imageStream = $this->getImageStreamForRotation(180);
        $image       = $this->getImage();

        $image->expects($this->once())
        ->method('rotate')
        ->with(180);

        $image->expects($this->once())
        ->method('get')
        ->will($this->returnValue($imageStream));

        $filter = new CorrectExifRotation();
        $this->assertSame($image, $filter->apply($image));
    }

    public function testCorrectExifRotationWithMinus90Deg()
    {
        $imageStream = $this->getImageStreamForRotation(-90);
        $image       = $this->getImage();

        $image->expects($this->once())
        ->method('rotate')
        ->with(-90);

        $image->expects($this->once())
        ->method('get')
        ->will($this->returnValue($imageStream));

        $filter = new CorrectExifRotation();
        $this->assertSame($image, $filter->apply($image));
    }

    public function testNoRotationWithUnknownValue()
    {
        $imageStream = $this->getImageStreamForRotation("unknown");
        $image       = $this->getImage();

        $image->expects($this->never())
        ->method('rotate');

        $image->expects($this->once())
        ->method('get')
        ->will($this->returnValue($imageStream));

        $filter = new CorrectExifRotation();
        $this->assertSame($image, $filter->apply($image));
    }

    public function testNoRotationWithMissingOrientation()
    {
        $imageStream = $this->getImageStreamForRotation("noOrientation");
        $image       = $this->getImage();

        $image->expects($this->never())
        ->method('rotate');

        $image->expects($this->once())
        ->method('get')
        ->will($this->returnValue($imageStream));

        $filter = new CorrectExifRotation();
        $this->assertSame($image, $filter->apply($image));
    }

    public function testCorrectExifRotationWith90DegAndColor()
    {
        $imageStream = $this->getImageStreamForRotation(90);
        $image = $this->getImage();
        $color = $this->getColor();

         $image->expects($this->once())
               ->method('rotate')
               ->with(90, $color);

         $image->expects($this->once())
                ->method('get')
                ->will($this->returnValue($imageStream));

        $filter = new CorrectExifRotation($color);
        $this->assertSame($image, $filter->apply($image));
    }

    private function getImageStreamForRotation($rotation, $extension='jpg')
    {
        $rotation = str_replace('-', 'minus', $rotation);
        $rotation = preg_replace('/[^[:alnum:]]/i', '', $rotation);
        $filename = __DIR__ . '/../../../Fixtures/exifOrientation/' .$rotation . '.' . $extension;
        if (!file_exists($filename)) {
            return '';
        }

        return file_get_contents($filename);
    }
}
