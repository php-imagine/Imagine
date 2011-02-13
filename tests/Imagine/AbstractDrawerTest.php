<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine;

abstract class AbstractDrawerTest extends \PHPUnit_Framework_TestCase
{
    public function testDrawASmileyFace()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(400, 300, new Color('000'));

        $canvas->draw()
            ->arc(200, 200, 200, 150, 0, 180, new Color('fff'))
            ->ellipse(125, 100, 50, 50, new Color('fff'))
            ->ellipse(275, 100, 50, 50, new Color('fff'));

        $canvas->save('tests/Imagine/Fixtures/smiley.jpg', array('quality' => 100));

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/smiley.jpg'));

        unlink('tests/Imagine/Fixtures/smiley.jpg');
    }

    public function testDrawAPieSlice()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(400, 300, new Color('000'));

        $canvas->draw()
            ->pieSlice(200, 150, 100, 200, 45, 135, new Color('fff'));

        $canvas->save('tests/Imagine/Fixtures/pie.jpg');

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/pie.jpg'));

        unlink('tests/Imagine/Fixtures/pie.jpg');
    }


    public function testDrawAChord()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(400, 300, new Color('000'));

        $canvas->draw()
            ->chord(200, 150, 100, 200, 45, 135, new Color('fff'), false);

        $canvas->save('tests/Imagine/Fixtures/chord.jpg');

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/chord.jpg'));

        unlink('tests/Imagine/Fixtures/chord.jpg');
    }

    public function testDrawALine()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(400, 300, new Color('000'));

        $canvas->draw()
            ->line(50, 50, 350, 250, new Color('fff'))
            ->line(50, 250, 350, 50, new Color('fff'));

        $canvas->save('tests/Imagine/Fixtures/lines.jpg', array(
            'quality' => 100
        ));

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/lines.jpg'));

        unlink('tests/Imagine/Fixtures/lines.jpg');
    }

    abstract protected function getImagine();
}
