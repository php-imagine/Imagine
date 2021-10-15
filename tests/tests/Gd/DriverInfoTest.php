<?php

namespace Imagine\Test\Gd;

use Imagine\Gd\DriverInfo;
use Imagine\Image\Format;
use Imagine\Test\Driver\AbstractDriverInfoTest;

/**
 * @group gd
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
        );
    }
}
