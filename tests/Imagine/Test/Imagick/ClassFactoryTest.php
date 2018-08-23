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

use Imagine\Imagick\Imagine;
use Imagine\Test\Factory\AbstractClassFactoryTest;

/**
 * @group ext-gd
 */
class ClassFactoryTest extends AbstractClassFactoryTest
{
    protected function setUp()
    {
        parent::setUp();
        
        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
        }
    }
    
    protected function getImagine()
    {
        return new Imagine();
    }
}
