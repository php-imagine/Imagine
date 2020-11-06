<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Image\Metadata;

use Imagine\Image\Metadata\MetadataReaderInterface;
use Imagine\Test\ImagineTestCase;

abstract class MetadataReaderTestCase extends ImagineTestCase
{
    /**
     * @return MetadataReaderInterface
     */
    abstract protected function getReader();

    public function testReadFromFile()
    {
        $source = IMAGINE_TEST_FIXTURESFOLDER . '/pixel-CMYK.jpg';
        $metadata = $this->getReader()->readFile($source);
        $this->assertInstanceOf('Imagine\Image\Metadata\MetadataBag', $metadata);
        $this->assertEquals(realpath($source), $metadata['filepath']);
        $this->assertEquals($source, $metadata['uri']);
    }

    public function testReadFromExifUncompatibleFile()
    {
        $source = IMAGINE_TEST_FIXTURESFOLDER . '/trans.png';
        $metadata = $this->getReader()->readFile($source);
        $this->assertInstanceOf('Imagine\Image\Metadata\MetadataBag', $metadata);
        $this->assertEquals(realpath($source), $metadata['filepath']);
        $this->assertEquals($source, $metadata['uri']);
    }

    public function testReadFromHttpFile()
    {
        $source = self::HTTP_IMAGE;
        try {
            $metadata = $this->getReader()->readFile($source);
        } catch (\Imagine\Exception\RuntimeException $x) {
            if (getenv('TRAVIS') && getenv('CONTINUOUS_INTEGRATION') && $x->getMessage() === 'gnutls_handshake() failed: A TLS packet with unexpected length was received.') {
                $this->markTestSkipped($x->getMessage());
            }
            throw $x;
        }
        $this->assertInstanceOf('Imagine\Image\Metadata\MetadataBag', $metadata);
        $this->assertFalse(isset($metadata['filepath']));
        $this->assertEquals($source, $metadata['uri']);
    }

    public function testReadFromInvalidFileThrowsAnException()
    {
        $this->isGoingToThrowException('Imagine\Exception\InvalidArgumentException', 'File /path/to/no/file does not exist.');
        $this->getReader()->readFile('/path/to/no/file');
    }

    public function testReadFromData()
    {
        $source = IMAGINE_TEST_FIXTURESFOLDER . '/pixel-CMYK.jpg';
        $metadata = $this->getReader()->readData(file_get_contents($source));
        $this->assertInstanceOf('Imagine\Image\Metadata\MetadataBag', $metadata);
    }

    public function testReadFromInvalidDataDoesNotThrowException()
    {
        $metadata = $this->getReader()->readData('this is nonsense');
        $this->assertInstanceOf('Imagine\Image\Metadata\MetadataBag', $metadata);
    }

    public function testReadFromStream()
    {
        $source = IMAGINE_TEST_FIXTURESFOLDER . '/pixel-CMYK.jpg';
        $resource = fopen($source, 'r');
        $metadata = $this->getReader()->readStream($resource);
        $this->assertInstanceOf('Imagine\Image\Metadata\MetadataBag', $metadata);
        $this->assertEquals(realpath($source), $metadata['filepath']);
        $this->assertEquals($source, $metadata['uri']);
    }

    public function testReadFromInvalidStreamThrowsAnException()
    {
        $this->isGoingToThrowException('Imagine\Exception\InvalidArgumentException', 'Invalid resource provided.');
        $metadata = $this->getReader()->readStream(false);
        $this->assertInstanceOf('Imagine\Image\Metadata\MetadataBag', $metadata);
    }
}
