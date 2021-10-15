<?php

namespace Imagine\Test\Imagick;

use Imagine\Imagick\DriverInfo;
use Imagine\Imagick\Imagine;
use Imagine\Test\Draw\AbstractAlphaBlendingAwareDrawerTest;

/**
 * @group imagick
 * @group always-skipped
 */
class AlphaBlendingAwareDrawerTest extends AbstractAlphaBlendingAwareDrawerTest
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
     * @see \Imagine\Test\Draw\AbstractAlphaBlendingAwareDrawerTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }
}
