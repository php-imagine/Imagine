<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Image;

use Imagine\Driver\Info;
use Imagine\Driver\InfoProvider;
use Imagine\Exception\NotSupportedException;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Test\ImagineTestCase;

abstract class AbstractLayersTest extends ImagineTestCase implements InfoProvider
{
    /**
     * @return \Imagine\Image\ImagineInterface
     */
    abstract protected function getImagine();

    /**
     * @param string|null $path
     *
     * @return \Imagine\Image\ImageInterface
     */
    abstract protected function getImage($path = null);

    abstract protected function assertLayersEquals(ImageInterface $expected, ImageInterface $actual);

    public function testMerge()
    {
        $palette = new RGB();
        $image = $this->getImagine()->create(new Box(20, 20), $palette->color('#FFFFFF'));
        foreach ($image->layers() as $layer) {
            $layer
                ->draw()
                ->polygon(array(new Point(0, 0), new Point(0, 20), new Point(20, 20), new Point(20, 0)), $palette->color('#FF0000'), true);
        }
        $image->layers()->merge();

        $this->assertEquals('#ff0000', (string) $image->getColorAt(new Point(5, 5)));
    }

    public function testLayerArrayAccess()
    {
        $firstImage = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');
        $secondImage = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/yellow.gif');
        $thirdImage = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/blue.gif');

        $layers = $firstImage->layers();

        $this->assertCount(1, $layers);

        $layers[] = $secondImage;

        $this->assertCount(2, $layers);
        $this->assertLayersEquals($firstImage, $layers[0]);
        $this->assertLayersEquals($secondImage, $layers[1]);

        $layers[1] = $thirdImage;

        $this->assertCount(2, $layers);
        $this->assertLayersEquals($firstImage, $layers[0]);
        $this->assertLayersEquals($thirdImage, $layers[1]);

        $layers[] = $secondImage;

        $this->assertCount(3, $layers);
        $this->assertLayersEquals($firstImage, $layers[0]);
        $this->assertLayersEquals($thirdImage, $layers[1]);
        $this->assertLayersEquals($secondImage, $layers[2]);

        $this->assertTrue(isset($layers[2]));
        $this->assertTrue(isset($layers[1]));
        $this->assertTrue(isset($layers[0]));

        unset($layers[1]);

        $this->assertCount(2, $layers);
        $this->assertLayersEquals($firstImage, $layers[0]);
        $this->assertLayersEquals($secondImage, $layers[1]);

        $this->assertFalse(isset($layers[2]));
        $this->assertTrue(isset($layers[1]));
        $this->assertTrue(isset($layers[0]));
    }

    public function testLayerAddGetSetRemove()
    {
        $firstImage = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');
        $secondImage = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/yellow.gif');
        $thirdImage = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/blue.gif');

        $layers = $firstImage->layers();

        $this->assertCount(1, $layers);

        $layers->add($secondImage);

        $this->assertCount(2, $layers);
        $this->assertLayersEquals($firstImage, $layers->get(0));
        $this->assertLayersEquals($secondImage, $layers->get(1));

        $layers->set(1, $thirdImage);

        $this->assertCount(2, $layers);
        $this->assertLayersEquals($firstImage, $layers->get(0));
        $this->assertLayersEquals($thirdImage, $layers->get(1));

        $layers->add($secondImage);

        $this->assertCount(3, $layers);
        $this->assertLayersEquals($firstImage, $layers->get(0));
        $this->assertLayersEquals($thirdImage, $layers->get(1));
        $this->assertLayersEquals($secondImage, $layers->get(2));

        $this->assertTrue($layers->has(2));
        $this->assertTrue($layers->has(1));
        $this->assertTrue($layers->has(0));

        $layers->remove(1);

        $this->assertCount(2, $layers);
        $this->assertLayersEquals($firstImage, $layers->get(0));
        $this->assertLayersEquals($secondImage, $layers->get(1));

        $this->assertFalse($layers->has(2));
        $this->assertTrue($layers->has(1));
        $this->assertTrue($layers->has(0));
    }

    /**
     * @dataProvider provideInvalidArguments
     *
     * @param mixed $offset
     */
    public function testLayerArrayAccessInvalidArgumentExceptions($offset)
    {
        try {
            $this->getDriverInfo()->requireFeature(Info::FEATURE_MULTIPLELAYERS);
        } catch (NotSupportedException $x) {
            $this->markTestSkipped($x->getMessage());
        }
        $this->isGoingToThrowException('Imagine\Exception\InvalidArgumentException');
        $firstImage = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');
        $secondImage = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');

        $layers = $firstImage->layers();
        $this->assertInstanceOf('Imagine\Image\LayersInterface', $layers);

        $layers[$offset] = $secondImage;
    }

    /**
     * @dataProvider provideOutOfBoundsArguments
     *
     * @param mixed $offset
     */
    public function testLayerArrayAccessOutOfBoundsExceptions($offset)
    {
        try {
            $this->getDriverInfo()->requireFeature(Info::FEATURE_MULTIPLELAYERS);
        } catch (NotSupportedException $x) {
            $this->markTestSkipped($x->getMessage());
        }
        $this->isGoingToThrowException('Imagine\Exception\OutOfBoundsException');
        $firstImage = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');
        $secondImage = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');

        $layers = $firstImage->layers();
        $this->assertInstanceOf('Imagine\Image\LayersInterface', $layers);

        $layers[$offset] = $secondImage;
    }

    public function testAnimateEmpty()
    {
        try {
            $this->getDriverInfo()->requireFeature(array(Info::FEATURE_MULTIPLELAYERS, Info::FEATURE_ADDLAYERSTOEMPTYIMAGE));
        } catch (NotSupportedException $x) {
            $this->markTestSkipped($x->getMessage());
        }
        $image = $this->getImage();
        $layers = $image->layers();
        $this->assertInstanceOf('Imagine\Image\LayersInterface', $layers);

        $layers[] = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');
        $layers[] = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/yellow.gif');
        $layers[] = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/blue.gif');

        $target = $this->getTemporaryFilename('.gif');

        $image->save($target, array(
            'animated' => true,
        ));
    }

    /**
     * @dataProvider provideAnimationParameters
     *
     * @param mixed $delay
     * @param mixed $loops
     */
    public function testAnimateWithParameters($delay, $loops)
    {
        try {
            $this->getDriverInfo()->requireFeature(Info::FEATURE_MULTIPLELAYERS);
        } catch (NotSupportedException $x) {
            $this->markTestSkipped($x->getMessage());
        }
        $image = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');
        $layers = $image->layers();
        $this->assertInstanceOf('Imagine\Image\LayersInterface', $layers);

        $layers[] = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/yellow.gif');
        $layers[] = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/blue.gif');

        $target = $this->getTemporaryFilename("delay={$delay}-loops={$loops}.gif");

        $image->save($target, array(
            'animated' => true,
            'animated.delay' => $delay,
            'animated.loops' => $loops,
        ));
    }

    public function provideAnimationParameters()
    {
        return array(
            array(0, 0),
            array(500, 0),
            array(0, 10),
            array(5000, 10),
        );
    }

    /**
     * @dataProvider provideWrongAnimationParameters
     *
     * @param mixed $delay
     * @param mixed $loops
     */
    public function testAnimateWithWrongParameters($delay, $loops)
    {
        try {
            $this->getDriverInfo()->requireFeature(Info::FEATURE_MULTIPLELAYERS);
        } catch (NotSupportedException $x) {
            $this->markTestSkipped($x->getMessage());
        }
        $this->isGoingToThrowException('Imagine\Exception\InvalidArgumentException');
        $image = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');
        $layers = $image->layers();
        $this->assertInstanceOf('Imagine\Image\LayersInterface', $layers);

        $layers[] = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/yellow.gif');
        $layers[] = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/blue.gif');

        $target = $this->getTemporaryFilename("delay={$delay}-loops={$loops}.gif");

        $image->save($target, array(
            'animated' => true,
            'animated.delay' => $delay,
            'animated.loops' => $loops,
        ));
    }

    public function provideWrongAnimationParameters()
    {
        return array(
            array(-1, 0),
            array(500, -1),
            array(-1, 10),
            array(0, -1),
        );
    }

    public function provideInvalidArguments()
    {
        return array(
            array('lambda'),
            array('0'),
            array('1'),
            array(1.0),
        );
    }

    public function provideOutOfBoundsArguments()
    {
        return array(
            array(-1),
            array(2),
        );
    }
}
