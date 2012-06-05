<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

use Imagine\Draw\AbstractDrawerTest;

class DrawerTest extends AbstractDrawerTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
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
