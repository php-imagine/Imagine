<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Draw;

use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Imagine\Test\ImagineTestCase;

abstract class AbstractDrawerTest extends ImagineTestCase
{
    public function testDrawASmileyFace()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('000'));

        $canvas->draw()
            ->chord(new Point(200, 200), new Box(200, 150), 0, 180, $this->getColor('fff'), false)
            ->ellipse(new Point(125, 100), new Box(50, 50), $this->getColor('fff'))
            ->ellipse(new Point(275, 100), new Box(50, 50), $this->getColor('fff'), true);

        $canvas->save('tests/Imagine/Fixtures/smiley.png');

        $this->assertFileExists('tests/Imagine/Fixtures/smiley.png');

        unlink('tests/Imagine/Fixtures/smiley.png');
    }

    public function drawACircleProvider()
    {
        return array(
            array(false, 1),
            array(true, 1),
            array(false, 2),
            array(true, 2),
        );
    }

    /**
     * @dataProvider drawACircleProvider
     *
     * @param bool $fill
     * @param int $thickness
     */
    public function testDrawACircle($fill, $thickness)
    {
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(15, 15), $this->getColor('fff'));
        $drawer = $image->draw();
        $this->assertSame($drawer, $drawer->circle(
            new Point(7, 7),
            6,
            $this->getColor('f00'),
            $fill,
            $thickness
        ));
        $expected = $imagine->open('tests/Imagine/Fixtures/drawer/circle/thinkness' . $thickness . '-filled' . ($fill ? '1' : '0') . '.png');
        $this->assertImageEquals($expected, $image, '', 0.107);
    }

    public function testDrawAnEllipse()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('000'));

        $canvas->draw()
            ->ellipse(new Center($canvas->getSize()), new Box(300, 200), $this->getColor('fff'), true);

        $canvas->save('tests/Imagine/Fixtures/ellipse.png');

        $this->assertFileExists('tests/Imagine/Fixtures/ellipse.png');

        unlink('tests/Imagine/Fixtures/ellipse.png');
    }

    public function testDrawAPieSlice()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('000'));

        $canvas->draw()
            ->pieSlice(new Point(200, 150), new Box(100, 200), 45, 135, $this->getColor('fff'), true);

        $canvas->save('tests/Imagine/Fixtures/pie.png');

        $this->assertFileExists('tests/Imagine/Fixtures/pie.png');

        unlink('tests/Imagine/Fixtures/pie.png');
    }

    public function testDrawAChord()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('000'));

        $canvas->draw()
            ->chord(new Point(200, 150), new Box(100, 200), 45, 135, $this->getColor('fff'), true);

        $canvas->save('tests/Imagine/Fixtures/chord.png');

        $this->assertFileExists('tests/Imagine/Fixtures/chord.png');

        unlink('tests/Imagine/Fixtures/chord.png');
    }

    public function testDrawALine()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('000'));

        $canvas->draw()
            ->line(new Point(50, 50), new Point(350, 250), $this->getColor('fff'))
            ->line(new Point(50, 250), new Point(350, 50), $this->getColor('fff'));

        $canvas->save('tests/Imagine/Fixtures/lines.png');

        $this->assertFileExists('tests/Imagine/Fixtures/lines.png');

        unlink('tests/Imagine/Fixtures/lines.png');
    }

    public function drawARectangleProvider()
    {
        return array(
            array(false, 1),
            array(true, 1),
            array(false, 2),
            array(true, 2),
        );
    }

    /**
     * @dataProvider drawARectangleProvider
     *
     * @param bool $fill
     * @param int $thickness
     */
    public function testDrawARectangle($fill, $thickness)
    {
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(15, 15), $this->getColor('fff'));
        $drawer = $image->draw();
        $this->assertSame($drawer, $drawer->rectangle(
            new Point(2, 2),
            new Point(12, 12),
            $this->getColor('f00'),
            $fill,
            $thickness
        ));
        $expected = $imagine->open('tests/Imagine/Fixtures/drawer/rectangle/thinkness' . $thickness . '-filled' . ($fill ? '1' : '0') . '.png');
        $this->assertImageEquals($expected, $image);
    }

    public function testDrawAPolygon()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('000'));

        $canvas->draw()
            ->polygon(array(
                new Point(50, 20),
                new Point(350, 20),
                new Point(350, 280),
                new Point(50, 280),
            ), $this->getColor('fff'), true);

        $canvas->save('tests/Imagine/Fixtures/polygon.png');

        $this->assertFileExists('tests/Imagine/Fixtures/polygon.png');

        unlink('tests/Imagine/Fixtures/polygon.png');
    }

    public function testDrawADot()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('000'));

        $canvas->draw()
            ->dot(new Point(200, 150), $this->getColor('fff'))
            ->dot(new Point(200, 151), $this->getColor('fff'))
            ->dot(new Point(200, 152), $this->getColor('fff'))
            ->dot(new Point(200, 153), $this->getColor('fff'));

        $canvas->save('tests/Imagine/Fixtures/dot.png');

        $this->assertFileExists('tests/Imagine/Fixtures/dot.png');

        unlink('tests/Imagine/Fixtures/dot.png');
    }

    public function testDrawAnArc()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('000'));
        $size = $canvas->getSize();

        $canvas->draw()
            ->arc(new Center($size), $size->scale(0.5), 0, 180, $this->getColor('fff'));

        $canvas->save('tests/Imagine/Fixtures/arc.png');

        $this->assertFileExists('tests/Imagine/Fixtures/arc.png');

        unlink('tests/Imagine/Fixtures/arc.png');
    }

    public function testDrawText()
    {
        if (!$this->isFontTestSupported()) {
            $this->markTestSkipped('This install does not support font tests');
        }

        $path = 'tests/Imagine/Fixtures/font/Arial.ttf';
        $black = $this->getColor('000');
        $file36 = 'tests/Imagine/Fixtures/bulat36.png';
        $file24 = 'tests/Imagine/Fixtures/bulat24.png';
        $file18 = 'tests/Imagine/Fixtures/bulat18.png';
        $file12 = 'tests/Imagine/Fixtures/bulat12.png';

        $imagine = $this->getImagine();
        $canvas = $imagine->create(new Box(400, 300), $this->getColor('fff'));
        $font = $imagine->font($path, 36, $black);

        $canvas->draw()
            ->text('Bulat', $font, new Point(0, 0), 135);

        $canvas->save($file36);

        unset($canvas);

        $this->assertFileExists($file36);

        unlink($file36);

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('fff'));
        $font = $imagine->font($path, 24, $black);

        $canvas->draw()
            ->text('Bulat', $font, new Point(24, 24));

        $canvas->save($file24);

        unset($canvas);

        $this->assertFileExists($file24);

        unlink($file24);

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('fff'));
        $font = $imagine->font($path, 18, $black);

        $canvas->draw()
            ->text('Bulat', $font, new Point(18, 18));

        $canvas->save($file18);

        unset($canvas);

        $this->assertFileExists($file18);

        unlink($file18);

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('fff'));
        $font = $imagine->font($path, 12, $black);

        $canvas->draw()
            ->text('Bulat', $font, new Point(12, 12));

        $canvas->save($file12);

        unset($canvas);

        $this->assertFileExists($file12);

        unlink($file12);
    }

    private function getColor($color)
    {
        static $palette;

        if (!$palette) {
            $palette = new RGB();
        }

        return $palette->color($color);
    }

    /**
     * @return ImagineInterface
     */
    abstract protected function getImagine();

    abstract protected function isFontTestSupported();
}
