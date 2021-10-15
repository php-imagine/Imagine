<?php

namespace Imagine\Test\Driver;

use Imagine\Driver\InfoProvider;
use Imagine\Image\Format;
use Imagine\Test\ImagineTestCase;

abstract class AbstractDriverInfoTest extends ImagineTestCase implements InfoProvider
{
    /**
     * Provide the IDs of the file formats that are available for sure.
     *
     * @return [string]
     */
    abstract public function provideRequiredFileFormat();

    /**
     * @dataProvider provideRequiredFileFormat
     *
     * @param mixed $formatID
     */
    public function testRequiredFileFormat($formatID)
    {
        $format = Format::get($formatID);
        $this->assertInstanceOf('Imagine\Image\Format', $format);
        $this->assertSame($format->getID(), $formatID);
        $driverInfo = $this->getDriverInfo();
        $this->assertTrue($driverInfo->isFormatSupported($format));
        $this->assertTrue($driverInfo->isFormatSupported($formatID));
        $this->assertSame($format, $driverInfo->getSupportedFormats()->find($format));
        $this->assertSame($format, $driverInfo->getSupportedFormats()->find($formatID));
    }
}
