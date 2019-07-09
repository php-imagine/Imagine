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

use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Test\ImagineTestCase;

abstract class AbstractImagineTest extends ImagineTestCase
{
    public function testShouldCreateEmptyImage()
    {
        $factory = $this->getImagine();
        $image = $factory->create(new Box(50, 50));
        $size = $image->getSize();

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(50, $size->getWidth());
        $this->assertEquals(50, $size->getHeight());
    }

    public function testShouldOpenAnImage()
    {
        $source = IMAGINE_TEST_FIXTURESFOLDER . '/google.png';
        $factory = $this->getImagine();
        $image = $factory->open($source);
        $size = $image->getSize();

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(364, $size->getWidth());
        $this->assertEquals(126, $size->getHeight());

        $metadata = $image->metadata();

        $this->assertEquals($source, $metadata['uri']);
        $this->assertEquals(realpath($source), $metadata['filepath']);
    }

    public function testShouldOpenAWebPImage()
    {
        $source = IMAGINE_TEST_FIXTURESFOLDER . '/webp-image.webp';
        $factory = $this->getImagine();
        $image = $factory->open($source);
        $size = $image->getSize();
        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(100, $size->getWidth());
        $this->assertEquals(100, $size->getHeight());
        $metadata = $image->metadata();
        $this->assertEquals($source, $metadata['uri']);
        $this->assertEquals(realpath($source), $metadata['filepath']);
    }

    public function testShouldOpenAnSplFileResource()
    {
        $source = IMAGINE_TEST_FIXTURESFOLDER . '/google.png';
        $resource = new \SplFileInfo($source);
        $factory = $this->getImagine();
        $image = $factory->open($resource);
        $size = $image->getSize();

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(364, $size->getWidth());
        $this->assertEquals(126, $size->getHeight());

        $metadata = $image->metadata();

        $this->assertEquals($source, $metadata['uri']);
        $this->assertEquals(realpath($source), $metadata['filepath']);
    }

    public function testShouldFailOnUnknownImage()
    {
        $invalidResource = __DIR__ . '/path/that/does/not/exist';

        $this->isGoingToThrowException('Imagine\Exception\InvalidArgumentException', sprintf('File %s does not exist.', $invalidResource));
        $this->getImagine()->open($invalidResource);
    }

    public function testShouldFailOnInvalidImage()
    {
        $source = IMAGINE_TEST_FIXTURESFOLDER . '/invalid-image.jpg';

        $this->isGoingToThrowException('Imagine\Exception\RuntimeException', sprintf('Unable to open image %s', $source));
        $this->getImagine()->open($source);
    }

    public function testShouldOpenAnHttpImage()
    {
        $factory = $this->getImagine();
        try {
            $image = $factory->open(self::HTTP_IMAGE);
        } catch (\Imagine\Exception\RuntimeException $x) {
            if (getenv('TRAVIS') && getenv('CONTINUOUS_INTEGRATION') && $x->getMessage() === 'gnutls_handshake() failed: A TLS packet with unexpected length was received.') {
                $this->markTestSkipped($x->getMessage());
            }
            throw $x;
        }
        $size = $image->getSize();

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(280, $size->getWidth());
        $this->assertEquals(140, $size->getHeight());

        $metadata = $image->metadata();

        $this->assertEquals(self::HTTP_IMAGE, $metadata['uri']);
        $this->assertArrayNotHasKey('filepath', $metadata);
    }

    public function testShouldCreateImageFromString()
    {
        $factory = $this->getImagine();
        $image = $factory->load(file_get_contents(IMAGINE_TEST_FIXTURESFOLDER . '/google.png'));
        $size = $image->getSize();

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(364, $size->getWidth());
        $this->assertEquals(126, $size->getHeight());

        $metadata = $image->metadata();

        $this->assertArrayNotHasKey('uri', $metadata);
        $this->assertArrayNotHasKey('filepath', $metadata);
    }

    public function testShouldCreateImageFromStreamWithMetadata()
    {
        $source = 'http://imagine.readthedocs.org/en/latest/_static/exit-90-test.jpg';
        $resource = fopen($source, 'r');

        $factory = $this->getImagine();
        $image = $factory->read($resource);
        $size = $image->getSize();

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(50, $size->getWidth());
        $this->assertEquals(50, $size->getHeight());

        $metadata = $image->metadata();

        $this->assertEquals($source, $metadata['uri']);
        $this->assertEquals(realpath($source), $metadata['filepath']);
        $this->assertEquals(6, $metadata['ifd0.Orientation']);
    }

    public function testShouldCreateImageFromResource()
    {
        $source = IMAGINE_TEST_FIXTURESFOLDER . '/google.png';
        $factory = $this->getImagine();
        $resource = fopen($source, 'r');
        $image = $factory->read($resource);
        $size = $image->getSize();

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(364, $size->getWidth());
        $this->assertEquals(126, $size->getHeight());

        $metadata = $image->metadata();

        $this->assertEquals($source, $metadata['uri']);
        $this->assertEquals(realpath($source), $metadata['filepath']);
    }

    public function testShouldCreateImageFromHttpResource()
    {
        $factory = $this->getImagine();
        $resource = fopen(self::HTTP_IMAGE, 'r');
        $image = $factory->read($resource);
        $size = $image->getSize();

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(280, $size->getWidth());
        $this->assertEquals(140, $size->getHeight());

        $metadata = $image->metadata();

        $this->assertEquals(self::HTTP_IMAGE, $metadata['uri']);
        $this->assertArrayNotHasKey('filepath', $metadata);
    }

    public function testCreateAlphaPrecision()
    {
        $imagine = $this->getImagine();
        $palette = new RGB();
        $image = $imagine->create(new Box(1, 1), $palette->color('#f00', 17));
        $actualColor = $image->getColorAt(new Point(0, 0));
        $this->assertEquals(17, $actualColor->getAlpha());
    }

    /**
     * @return ImagineInterface
     */
    abstract protected function getImagine();
}
