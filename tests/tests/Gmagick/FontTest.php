<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Gmagick;

use Imagine\Gmagick\DriverInfo;
use Imagine\Gmagick\Imagine;
use Imagine\Test\Image\AbstractFontTest;

/**
 * @group gmagick
 */
class FontTest extends AbstractFontTest
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
     * @see \Imagine\Test\Image\AbstractFontTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }
}
