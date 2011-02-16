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

use Imagine\Cartesian\Coordinate;
use Imagine\Cartesian\Size;

abstract class AbstractDrawerTest extends \PHPUnit_Framework_TestCase
{
    public function testDrawASmileyFace()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Size(400, 300), new Color('000'));

        $canvas->draw()
            ->chord(new Coordinate(200, 200), 200, 150, 0, 180, new Color('fff'), false)
            ->ellipse(new Coordinate(125, 100), 50, 50, new Color('fff'))
            ->ellipse(new Coordinate(275, 100), 50, 50, new Color('fff'));

        $canvas->save('tests/Imagine/Fixtures/smiley.png', array('quality' => 100));

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/smiley.png'));

        unlink('tests/Imagine/Fixtures/smiley.png');
    }

    public function testDrawAPieSlice()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Size(400, 300), new Color('000'));

        $canvas->draw()
            ->pieSlice(new Coordinate(200, 150), 100, 200, 45, 135, new Color('fff'), true);

        $canvas->save('tests/Imagine/Fixtures/pie.png');

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/pie.png'));

        unlink('tests/Imagine/Fixtures/pie.png');
    }


    public function testDrawAChord()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Size(400, 300), new Color('000'));

        $canvas->draw()
            ->chord(new Coordinate(200, 150), 100, 200, 45, 135, new Color('fff'), true);

        $canvas->save('tests/Imagine/Fixtures/chord.png');

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/chord.png'));

        unlink('tests/Imagine/Fixtures/chord.png');
    }

    public function testDrawALine()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Size(400, 300), new Color('000'));

        $canvas->draw()
            ->line(new Coordinate(50, 50), new Coordinate(350, 250), new Color('fff'))
            ->line(new Coordinate(50, 250), new Coordinate(350, 50), new Color('fff'));

        $canvas->save('tests/Imagine/Fixtures/lines.png', array(
            'quality' => 100
        ));

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/lines.png'));

        unlink('tests/Imagine/Fixtures/lines.png');
    }

    public function testDrawAPolygon()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Size(400, 300), new Color('000'));

        $canvas->draw()
            ->polygon(array(
                new Coordinate(50, 20),
                new Coordinate(350, 20),
                new Coordinate(350, 280),
                new Coordinate(50, 280),
            ), new Color('fff'), true);

        $canvas->save('tests/Imagine/Fixtures/polygon.png', array(
            'quality' => 100
        ));

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/polygon.png'));

        unlink('tests/Imagine/Fixtures/polygon.png');
    }

    public function testDrawADot()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Size(400, 300), new Color('000'));

        $canvas->draw()
            ->dot(new Coordinate(200, 150), new Color('fff'))
            ->dot(new Coordinate(200, 151), new Color('fff'))
            ->dot(new Coordinate(200, 152), new Color('fff'))
            ->dot(new Coordinate(200, 153), new Color('fff'));

        $canvas->save('tests/Imagine/Fixtures/dot.png', array(
            'quality' => 100
        ));

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/dot.png'));

        unlink('tests/Imagine/Fixtures/dot.png');
    }

    abstract protected function getImagine();
}
