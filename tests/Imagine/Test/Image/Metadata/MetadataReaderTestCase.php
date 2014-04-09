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

/**
 */
abstract class MetadataReaderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return MetadataReaderInterface
     */
    abstract protected function getReader();

    public function testReadFromFile()
    {
        $source = __DIR__ . '/../../../Fixtures/pixel-CMYK.jpg';
        $metadata = $this->getReader()->readFile($source);
        $this->assertInstanceOf('Imagine\Image\Metadata\MetadataBag', $metadata);
        $this->assertEquals(realpath($source), $metadata['filepath']);
    }

    /**
     * @expectedException Imagine\Exception\InvalidArgumentException
     * @expectedExceptionMessage File /path/to/no/file does not exist.
     */
    public function testReadFromInvalidFileThrowsAnException()
    {
        $this->getReader()->readFile('/path/to/no/file');
    }

    public function testReadFromData()
    {
        $source = __DIR__ . '/../../../Fixtures/pixel-CMYK.jpg';
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
        $source = fopen(__DIR__ . '/../../../Fixtures/pixel-CMYK.jpg', 'r');
        $metadata = $this->getReader()->readStream($source);
        $this->assertInstanceOf('Imagine\Image\Metadata\MetadataBag', $metadata);
    }

    /**
     * @expectedException Imagine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid resource provided.
     */
    public function testReadFromInvalidStreamThrowsAnException()
    {
        $metadata = $this->getReader()->readStream(false);
        $this->assertInstanceOf('Imagine\Image\Metadata\MetadataBag', $metadata);
    }
}
