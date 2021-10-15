<?php

namespace Imagine\Test\Imagick;

use Imagine\Imagick\DriverInfo;
use Imagine\Imagick\Imagine;
use Imagine\Test\Constraint\AbstractIsImageEqualTest;

/**
 * @group imagick
 */
class IsImageEqualTest extends AbstractIsImageEqualTest
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
     * @see \Imagine\Test\Constraint\AbstractIsImageEqualTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }
}
