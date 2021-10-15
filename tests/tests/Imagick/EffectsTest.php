<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Imagick;

use Imagine\Imagick\DriverInfo;
use Imagine\Imagick\Imagine;
use Imagine\Test\Effects\AbstractEffectsTest;

/**
 * @group imagick
 */
class EffectsTest extends AbstractEffectsTest
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
     * @see \Imagine\Test\Effects\AbstractEffectsTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }
}
