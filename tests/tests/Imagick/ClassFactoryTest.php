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
use Imagine\Test\Factory\AbstractClassFactoryTest;

/**
 * @group imagick
 */
class ClassFactoryTest extends AbstractClassFactoryTest
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
     * @see \Imagine\Test\Factory\AbstractClassFactoryTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }
}
