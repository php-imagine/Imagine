<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Imagick;

use Imagine\Image\ImageInterface;
use Imagine\Image\Metadata\MetadataBag;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use Imagine\Imagick\Image;
use Imagine\Image\Palette\CMYK;
use Imagine\Image\Palette\RGB;
use Imagine\Test\Image\AbstractImageTest;
use Imagine\Image\Box;

/**
 * @group ext-imagick
 */
class ImageTest extends AbstractImageTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
        }
    }

    protected function tearDown()
    {
        if (class_exists('Imagick')) {
            $prop = new \ReflectionProperty('Imagine\Imagick\Image', 'supportsColorspaceConversion');
            $prop->setAccessible(true);
            $prop->setValue(null);
        }

        parent::tearDown();
    }

    protected function getImagine()
    {
        return new Imagine();
    }

    public function testImageRotateAndCropUsesTheCorrectCanvas()
    {
        $rgb = new RGB();
        // Create a single image 30x30 black
        $imageColor = $rgb->color('#000000', 100);
        $imagine = $this->getImagine()->create(new Box(30, 30), $imageColor);

        // Prepare for rotation: set the background color to white
        $imageBackgroundColor = $rgb->color('#FFFFFF', 100);
        $imagine->rotate(45, $imageBackgroundColor);

        // Crop to a 50x50 box from the image center
        $imagine->crop(new Point(15, 15), new Box(50, 50));

        $colorPoints = [
            // Center
            ['point' => new Point(22, 22), 'color' => $rgb->color('#000000', 100)],
            // Corners
            ['point' => new Point(5, 5), 'color' => $rgb->color('#FFFFFF', 30)],
            ['point' => new Point(5, 39), 'color' => $rgb->color('#FFFFFF', 30)],
            ['point' => new Point(39, 5), 'color' => $rgb->color('#FFFFFF', 30)],
            ['point' => new Point(39, 39), 'color' => $rgb->color('#FFFFFF', 30)],
            // Side spikes of the rotated square
            ['point' => new Point(5, 22), 'color' => $rgb->color('#000000', 100)],
            ['point' => new Point(22, 5), 'color' => $rgb->color('#000000', 100)],
            ['point' => new Point(39, 22), 'color' => $rgb->color('#000000', 100)],
            ['point' => new Point(22, 22), 'color' => $rgb->color('#000000', 100)],
        ];

        foreach ($colorPoints as $colorPoint) {
            /** @var Point $point */
            $point = $colorPoint['point'];
            $this->assertEquals(
                $colorPoint['color'],
                $imagine->getColorAt($point),
                'Point at [' . $point->getX().','.$point->getY().'] Does not match the expected color '
            );
        }
    }

    public function testImageResizeUsesProperMethodBasedOnInputAndOutputSizes()
    {
        $imagine = $this->getImagine();

        $image = $imagine->open('tests/Imagine/Fixtures/resize/210-design-19933.jpg');

        $image
            ->resize(new Box(1500, 750))
            ->save('tests/Imagine/Fixtures/resize/large.png')
        ;

        $image
            ->resize(new Box(100, 50))
            ->save('tests/Imagine/Fixtures/resize/small.png')
        ;

        unlink('tests/Imagine/Fixtures/resize/large.png');
        unlink('tests/Imagine/Fixtures/resize/small.png');
    }

    public function testAnimatedGifResize()
    {
        $imagine = $this->getImagine();
        $image = $imagine->open('tests/Imagine/Fixtures/anima3.gif');
        $image
            ->resize(new Box(150, 100))
            ->save('tests/Imagine/Fixtures/resize/anima3-150x100-actual.gif', array('animated' => true))
        ;
        $this->assertImageEquals(
            $imagine->open('tests/Imagine/Fixtures/resize/anima3-150x100.gif'),
            $imagine->open('tests/Imagine/Fixtures/resize/anima3-150x100-actual.gif')
        );
        unlink('tests/Imagine/Fixtures/resize/anima3-150x100-actual.gif');
    }

    // Older imagemagick versions does not support colorspace conversion
    public function testOlderImageMagickDoesNotAffectColorspaceUsageOnConstruct()
    {
        $palette = new CMYK();
        $imagick = $this->getMockBuilder('\Imagick')->getMock();
        $imagick->expects($this->any())
            ->method('setColorspace')
            ->will($this->throwException(new \RuntimeException('Method not supported')));

        $prop = new \ReflectionProperty('Imagine\Imagick\Image', 'supportsColorspaceConversion');
        $prop->setAccessible(true);
        $prop->setValue(false);

        return new Image($imagick, $palette, new MetadataBag());
    }

    /**
     * @depends testOlderImageMagickDoesNotAffectColorspaceUsageOnConstruct
     * @expectedException Imagine\Exception\RuntimeException
     * @expectedExceptionMessage Your version of Imagick does not support colorspace conversions.
     */
    public function testOlderImageMagickDoesNotAffectColorspaceUsageOnPaletteChange($image)
    {
        $image->usePalette(new RGB());
    }

    public function testAnimatedGifCrop()
    {
        $imagine = $this->getImagine();
        $image = $imagine->open('tests/Imagine/Fixtures/anima3.gif');
        $image
            ->crop(
                new Point(0, 0),
                new Box(150, 100)
            )
            ->save('tests/Imagine/Fixtures/crop/anima3-topleft-actual.gif', array('animated' => true))
        ;
        $this->assertImageEquals(
            $imagine->open('tests/Imagine/Fixtures/crop/anima3-topleft.gif'),
            $imagine->open('tests/Imagine/Fixtures/crop/anima3-topleft-actual.gif')
        );
        unlink('tests/Imagine/Fixtures/crop/anima3-topleft-actual.gif');
    }


    protected function supportMultipleLayers()
    {
        return true;
    }

    protected function getImageResolution(ImageInterface $image)
    {
        return $image->getImagick()->getImageResolution();
    }

    protected function getSamplingFactors(ImageInterface $image)
    {
        return $image->getImagick()->getSamplingFactors();
    }
}
