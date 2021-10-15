<?php

namespace Imagine\Test\Imagick;

use Imagine\Image\Format;
use Imagine\Imagick\DriverInfo;
use Imagine\Test\Driver\AbstractDriverInfoTest;

/**
 * @group imagick
 */
class DriverInfoTest extends AbstractDriverInfoTest
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
     * @see \Imagine\Test\Driver\AbstractDriverInfoTest::provideRequiredFileFormat()
     */
    public function provideRequiredFileFormat()
    {
        return array(
            array(Format::ID_GIF),
            array(Format::ID_JPEG),
            array(Format::ID_PNG),
            array(Format::ID_WBMP),
            array(Format::ID_XBM),
        );
    }
}
