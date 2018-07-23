<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Gmagick;

use Imagine\Gmagick\Imagine;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Imagine\Test\Image\AbstractImageTest;

/**
 * @group ext-gmagick
 */
class ImageTest extends AbstractImageTest
{
    protected function setUp()
    {
        parent::setUp();

        // disable GC while https://bugs.php.net/bug.php?id=63677 is still open
        // If GC enabled, Gmagick unit tests fail
        gc_disable();

        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick is not installed');
        }
    }

    public function provideFromAndToPalettes()
    {
        return array(
            array(
                'Imagine\Image\Palette\RGB',
                'Imagine\Image\Palette\CMYK',
                array(10, 10, 10),
            ),
            array(
                'Imagine\Image\Palette\CMYK',
                'Imagine\Image\Palette\RGB',
                array(10, 10, 10, 0),
            ),
        );
    }

    public function providePalettes()
    {
        return array(
            array('Imagine\Image\Palette\RGB', array(255, 0, 0)),
            array('Imagine\Image\Palette\CMYK', array(10, 0, 0, 0)),
        );
    }

    public function testPaletteIsGrayIfGrayImage()
    {
        $this->markTestSkipped('Gmagick does not support Gray colorspace, because of the lack omg image type support');
    }

    public function testGetColorAtCMYK()
    {
        $this->markTestSkipped('Gmagick fails to read CMYK colors properly, see https://bugs.php.net/bug.php?id=67435');
    }

    public function testImageCreatedAlpha()
    {
        $this->markTestSkipped('Alpha transparency is not supported by Gmagick');
    }

    public function testFillAlphaPrecision()
    {
        $this->markTestSkipped('Alpha transparency is not supported by Gmagick');
    }


    protected function getImagine()
    {
        return new Imagine();
    }

    protected function supportMultipleLayers()
    {
        return true;
    }

    protected function getImageResolution(ImageInterface $image)
    {
        return $image->getGmagick()->getimageresolution();
    }

    protected function getSamplingFactors(ImageInterface $image)
    {
        return $image->getGmagick()->getSamplingFactors();
    }
}
