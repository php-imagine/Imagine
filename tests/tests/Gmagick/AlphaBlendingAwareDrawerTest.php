<?php

namespace Imagine\Test\Gmagick;

use Imagine\Gmagick\DriverInfo;
use Imagine\Gmagick\Imagine;
use Imagine\Test\Draw\AbstractAlphaBlendingAwareDrawerTest;

/**
 * @group gmagick
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
