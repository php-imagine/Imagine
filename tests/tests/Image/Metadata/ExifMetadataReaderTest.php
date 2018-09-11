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

use Imagine\Image\Metadata\ExifMetadataReader;

class ExifMetadataReaderTest extends MetadataReaderTestCase
{
    protected function getReader()
    {
        return new ExifMetadataReader();
    }

    public function testExifDataAreReadWithReadFile()
    {
        $metadata = $this->getReader()->readFile(IMAGINE_TEST_FIXTURESFOLDER . '/exifOrientation/90.jpg');
        $this->assertTrue(isset($metadata['ifd0.Orientation']));
        $this->assertEquals(6, $metadata['ifd0.Orientation']);
    }

    public function testExifDataAreReadWithReadHttpFile()
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
        $this->assertEquals(1, $metadata['ifd0.Orientation']);
    }

    public function testExifDataAreReadWithReadData()
    {
        try {
            $metadata = $this->getReader()->readData(file_get_contents(IMAGINE_TEST_FIXTURESFOLDER . '/exifOrientation/90.jpg'));
        } catch (\Imagine\Exception\RuntimeException $x) {
            if (getenv('TRAVIS') && getenv('CONTINUOUS_INTEGRATION') && $x->getMessage() === 'gnutls_handshake() failed: A TLS packet with unexpected length was received.') {
                $this->markTestSkipped($x->getMessage());
            }
            throw $x;
        }
        $this->assertTrue(isset($metadata['ifd0.Orientation']));
        $this->assertEquals(6, $metadata['ifd0.Orientation']);
    }

    public function testExifDataAreReadWithReadStream()
    {
        $metadata = $this->getReader()->readStream(fopen(IMAGINE_TEST_FIXTURESFOLDER . '/exifOrientation/90.jpg', 'r'));
        $this->assertTrue(isset($metadata['ifd0.Orientation']));
        $this->assertEquals(6, $metadata['ifd0.Orientation']);
    }

    public function testReadingUnsupportedFile()
    {
        $metadata = $this->getReader()->readData('not valid');
        $this->assertSame(0, $metadata->count());
    }
}
