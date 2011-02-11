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

class DrawerTest extends \PHPUnit_Framework_TestCase
{
    public function testDrawASmileyFace()
    {
        $imagine = new Imagine();

        $canvas = $imagine->create(400, 300, new Color('000'));

        $drawing = $canvas->draw();

        $drawing->arc(200, 200, 200, 150, 0, 180, new Color('fff'))
            ->ellipse(125, 100, 50, 50, new Color('fff'))
            ->ellipse(275, 100, 50, 50, new Color('fff'));

        $canvas->save('tests/Imagine/Fixtures/smiley.jpg', array('quality' => 100));

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/smiley.jpg'));

        unlink('tests/Imagine/Fixtures/smiley.jpg');
    }

    public function testDrawAPieSlice()
    {
        $imagine = new Imagine();

        $canvas = $imagine->create(400, 300, new Color('000'));

        $drawing = $canvas->draw();

        $drawing->pieSlice(200, 150, 100, 200, 0, 180, new Color('fff'), true);

        $canvas->save('tests/Imagine/Fixtures/pie.jpg');
    }
}
