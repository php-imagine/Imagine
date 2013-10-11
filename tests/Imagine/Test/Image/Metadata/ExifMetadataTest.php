<?php
namespace Imagine\Test\Image;

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Imagine\Image\Metadata\ExifMetadata;
use Imagine\Image\Metadata\MetadataInterface;

/**
 */
class ExifMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getOrientationReturnsNullIfNoRotationIsGiven()
    {
        $metadata = new ExifMetadata($this->getImageMock('large.jpg'));
        $this->assertNull($metadata->getOrientation());
    }

    /**
     * @test
     */
    public function getOrientationReturnsCorrectRotationIfExifDataSaysSo()
    {
        $metadata = new ExifMetadata($this->getImageMock('exifOrientation/90.jpg'));
        $this->assertSame(MetadataInterface::ORIENTATION_ROTATED_MINUS90, $metadata->getOrientation());
    }

    /**
     * Gets an image mock for use with the ExifMetadata constructor
     *
     * @param string $fixtureImage
     * @return \Imagine\Image\ImageInterface
     */
    protected function getImageMock($fixtureImage)
    {
        $mock = $this->getMock('Imagine\Image\ImageInterface');
        $mock->expects($this->atLeastOnce())->method('get')->with('jpg')->will($this->returnValue(file_get_contents('tests/Imagine/Fixtures/' . $fixtureImage)));
        return $mock;
    }
}
