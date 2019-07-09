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
use Imagine\Test\Factory\AbstractClassFactoryTest;

/**
 * @group gd
 */
class ClassFactoryTest extends AbstractClassFactoryTest
{
    protected function setUpBase()
    {
        parent::setUpBase();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
    }

    protected function getImagine()
    {
        return new Imagine();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Factory\AbstractClassFactoryTest::canTestFont()
     */
    protected function canTestFont()
    {
        $info = gd_info();

        return (bool) $info['FreeType Support'];
    }
}
