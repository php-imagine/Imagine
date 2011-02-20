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

use Imagine\Color;

class FontTest extends \PHPUnit_Framework_TestCase
{

    public function testGetSize()
    {
        $path   = 'tests/Imagine/Fixtures/font/Arial.ttf';
        $black  = new Color('000', 100);
        $file36 = 'tests/Imagine/Fixtures/bulat36.png';
        $file24 = 'tests/Imagine/Fixtures/bulat24.png';
        $file18 = 'tests/Imagine/Fixtures/bulat18.png';
        $file12 = 'tests/Imagine/Fixtures/bulat12.png';

        $font = new Font($path, 36, $black);

        $font->mask('Bulat')
            ->save($file36);

        $this->assertTrue(file_exists($file36));

        unlink($file36);

        $font = new Font($path, 24, $black);

        $font->mask('Bulat')
            ->save($file24);

        $this->assertTrue(file_exists($file24));

        unlink($file24);

        $font = new Font($path, 18, $black);

        $font->mask('Bulat')
            ->save($file18);

        $this->assertTrue(file_exists($file18));

        unlink($file18);

        $font = new Font($path, 12, $black);

        $font->mask('Bulat')
            ->save($file12);

        $this->assertTrue(file_exists($file12));

        unlink($file12);
    }
}
