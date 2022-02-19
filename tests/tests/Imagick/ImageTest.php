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

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Imagick\DriverInfo;
use Imagine\Imagick\Imagine;
use Imagine\Test\Image\AbstractImageTest;

/**
 * @group imagick
 */
class ImageTest extends AbstractImageTest
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\InfoProvider::getDriverInfo()
     */
    public static function getDriverInfo($required = true)
    {
        return DriverInfo::get($required);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::getImageResolution()
     */
    protected function getImageResolution(ImageInterface $image)
    {
        return $image->getImagick()->getImageResolution();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::getSamplingFactors()
     */
    protected function getSamplingFactors(ImageInterface $image)
    {
        return $image->getImagick()->getSamplingFactors();
    }

    /**
     * @dataProvider provideFromAndToPalettes
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testUsePalette()
     */
    public function testUsePalette($from, $to, $color)
    {
        parent::testUsePalette($from, $to, $color);
    }

    /**
     * @dataProvider provideVariousSources
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testResolutionOnSave()
     */
    public function testResolutionOnSave($source)
    {
        parent::testResolutionOnSave($source);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testImageResizeUsesProperMethodBasedOnInputAndOutputSizes()
    {
        $imagine = $this->getImagine();

        $image = $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/resize/210-design-19933.jpg');

        $filenameLarge = $this->getTemporaryFilename('large.png');
        $image
            ->resize(new Box(1500, 750))
            ->save($filenameLarge)
        ;

        $filenameLarge = $this->getTemporaryFilename('small.png');
        $image
            ->resize(new Box(100, 50))
            ->save($filenameLarge)
        ;
    }

    public function testAnimatedGifResize()
    {
        $imagine = $this->getImagine();
        $image = $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/anima3.gif');
        $filename = $this->getTemporaryFilename('.gif');
        $image
            ->resize(new Box(150, 100))
            ->save($filename, array('animated' => true))
        ;
        $this->assertImageEquals(
            $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/resize/anima3-150x100.gif'),
            $imagine->open($filename)
        );
    }

    public function testAnimatedGifCrop()
    {
        $imagine = $this->getImagine();
        $image = $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/anima3.gif');
        $filename = $this->getTemporaryFilename('.gif');
        $image
            ->crop(
                new Point(0, 0),
                new Box(150, 100)
            )
            ->save($filename, array('animated' => true))
        ;
        $this->assertImageEquals(
            $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/crop/anima3-topleft.gif'),
            $imagine->open($filename)
        );
    }

    public function testOptimize()
    {
        $imagine = $this->getImagine();
        $rgb = new RGB();
        $image = $imagine->create(new Box(100, 100), $rgb->color('#fff'));
        $blackFrame = $imagine->create($image->getSize(), $rgb->color('#000'));
        $image->layers()->add(clone $blackFrame)->add(clone $blackFrame)->add(clone $blackFrame)->add(clone $blackFrame);
        $originalFilename = $this->getTemporaryFilename('not-optimized.gif');
        $image->save($originalFilename, array('animated' => true, 'optimize' => false));
        $originalSize = filesize($originalFilename);
        $optimizedFilename = $this->getTemporaryFilename('optimized.gif');
        $image->save($optimizedFilename, array('animated' => true, 'optimize' => true));
        $optimizedSize = filesize($optimizedFilename);
        $this->assertLessThan($originalSize, $optimizedSize);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testOptimizeWithDifferentFrameSizes()
    {
        $imagine = $this->getImagine();
        $rgb = new RGB();
        $image = $imagine->create(new Box(10, 10), $rgb->color('#fff'));
        $image->layers()->add($imagine->create($image->getSize()->scale(2)), $rgb->color('#fff'));
        $filename = $this->getTemporaryFilename('.gif');
        $image->save($filename, array('animated' => true, 'optimize' => true));
    }
}
