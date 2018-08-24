<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Gd;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Test\Image\AbstractImagineTest;

/**
 * @group ext-gd
 */
class ImagineTest extends AbstractImagineTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
    }

    protected function getEstimatedFontBox()
    {
        return new Box(
            DIRECTORY_SEPARATOR === '\\' ? 114 : 112,
            PHP_VERSION_ID >= 50600 ? 45 : 46
        );
    }

    protected function getImagine()
    {
        return new Imagine();
    }

    protected function isFontTestSupported()
    {
        $infos = gd_info();

        return isset($infos['FreeType Support']) ? $infos['FreeType Support'] : false;
    }
}
