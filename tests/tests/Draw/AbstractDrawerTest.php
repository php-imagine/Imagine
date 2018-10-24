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
    public function thicknessProvider()
    {
        return array(
            array(0),
            array(1),
            array(4),
        );
    }

    public function thicknessAndFillProvider()
    {
        $result = array();
        foreach ($this->thicknessProvider() as $thicknessData) {
            $result[] = array_merge($thicknessData, array(false));
            $result[] = array_merge($thicknessData, array(true));
        }

        return $result;
    }

    /**
     * @dataProvider thicknessProvider
     *
     * @param int $thickness
     */
    public function testArc($thickness)
    {
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(40, 30), $this->getColor('fff'));
        $size = $image->getSize();
        $drawer = $image->draw();
        $this->assertSame($drawer, $drawer->arc(new Center($size), $size->scale(0.5), 0, 180, $this->getColor('f00')));
        $filename = $this->getTemporaryFilename("thinkness{$thickness}.png");
        $image->save($filename);
        $this->assertImageEquals(IMAGINE_TEST_FIXTURESFOLDER . "/drawer/arc/thinkness{$thickness}.png", $filename, '', 0.134, $imagine);
    }

    /**
     * @dataProvider thicknessAndFillProvider
     *
     * @param int $thickness
     * @param bool $fill
     */
    public function testChord($thickness, $fill)
    {
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(60, 50), $this->getColor('fff'));
        $fill01 = $fill ? 1 : 0;
        $size = $image->getSize();
        $drawer = $image->draw();
        $this->assertSame($drawer, $drawer->chord(new Center($size), $size->scale(0.8), 0, 240, $this->getColor('f00'), $fill, $thickness));
        $filename = $this->getTemporaryFilename("thinkness{$thickness}-fill{$fill01}.png");
        $image->save($filename);
        $this->assertImageEquals(IMAGINE_TEST_FIXTURESFOLDER . "/drawer/chord/thinkness{$thickness}-fill{$fill01}.png", $filename, '', 0.153, $imagine);
    }

    /**
     * @dataProvider thicknessAndFillProvider
     *
     * @param bool $fill
     * @param int $thickness
     */
    public function testCircle($thickness, $fill)
    {
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(20, 20), $this->getColor('fff'));
        $size = $image->getSize();
        $fill01 = $fill ? 1 : 0;
        $drawer = $image->draw();
        $this->assertSame($drawer, $drawer->circle(new Point((int) $size->getWidth() / 2, (int) $size->getHeight() / 2), 0.8 * min($size->getWidth(), $size->getHeight()) / 2, $this->getColor('f00'), $fill, $thickness));
        $filename = $this->getTemporaryFilename("thinkness{$thickness}-fill{$fill01}.png");
        $image->save($filename);
        $this->assertImageEquals(IMAGINE_TEST_FIXTURESFOLDER . "/drawer/circle/thinkness{$thickness}-fill{$fill01}.png", $filename, '', 0.56, $imagine);
    }

    /**
     * @dataProvider thicknessAndFillProvider
     *
     * @param bool $fill
     * @param int $thickness
     */
    public function testEllipse($thickness, $fill)
    {
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(30, 20), $this->getColor('fff'));
        $size = $image->getSize();
        $fill01 = $fill ? 1 : 0;
        $drawer = $image->draw();
        $this->assertSame($drawer, $drawer->ellipse(new Center($size), $size->scale(0.9), $this->getColor('f00'), $fill, $thickness));
        $filename = $this->getTemporaryFilename("thinkness{$thickness}-fill{$fill01}.png");
        $image->save($filename);
        $this->assertImageEquals(IMAGINE_TEST_FIXTURESFOLDER . "/drawer/ellipse/thinkness{$thickness}-fill{$fill01}.png", $filename, '', 0.434, $imagine);
    }

    /**
     * @dataProvider thicknessProvider
     *
     * @param bool $fill
     * @param mixed $thickness
     */
    public function testLine($thickness)
    {
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(30, 20), $this->getColor('fff'));
        $size = $image->getSize();
        $drawer = $image->draw();
        $this->assertSame($drawer, $drawer->line(new Point(5, 5), new Point($size->getWidth() - 5, $size->getHeight() - 6), $this->getColor('f00'), $thickness));
        $filename = $this->getTemporaryFilename("thinkness{$thickness}.png");
        $image->save($filename);
        $this->assertImageEquals(IMAGINE_TEST_FIXTURESFOLDER . "/drawer/line/thinkness{$thickness}.png", $filename, '', 0.09, $imagine);
    }

    /**
     * @dataProvider thicknessAndFillProvider
     *
     * @param bool $fill
     * @param int $thickness
     */
    public function testPieSlice($thickness, $fill)
    {
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(40, 40), $this->getColor('fff'));
        $size = $image->getSize();
        $fill01 = $fill ? 1 : 0;
        $drawer = $image->draw();
        $this->assertSame($drawer, $drawer->pieSlice(new Point($size->getWidth() / 2, 5), $size->scale(0.9), 45, 135, $this->getColor('f00'), $fill, $thickness));
        $filename = $this->getTemporaryFilename("thinkness{$thickness}-fill{$fill01}.png");
        $image->save($filename);
        $this->assertImageEquals(IMAGINE_TEST_FIXTURESFOLDER . "/drawer/pieslice/thinkness{$thickness}-fill{$fill01}.png", $filename, '', 0.095, $imagine);
    }

    public function testDot()
    {
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(3, 3), $this->getColor('fff'));
        $drawer = $image->draw();
        $this->assertSame($drawer, $drawer->dot(new Point(1, 1), $this->getColor('f00')));
        $filename = $this->getTemporaryFilename('.png');
        $image->save($filename);
        $this->assertImageEquals(IMAGINE_TEST_FIXTURESFOLDER . '/drawer/dot/dot.png', $filename, '', 0.23, $imagine);
    }

    /**
     * @dataProvider thicknessAndFillProvider
     *
     * @param bool $fill
     * @param int $thickness
     */
    public function testRectangle($thickness, $fill)
    {
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(20, 25), $this->getColor('fff'));
        $size = $image->getSize();
        $fill01 = $fill ? 1 : 0;
        $drawer = $image->draw();
        $this->assertSame($drawer, $drawer->rectangle(new Point(5, 5), new Point($size->getWidth() - 5, $size->getHeight() - 5), $this->getColor('f00'), $fill, $thickness));
        $filename = $this->getTemporaryFilename("thinkness{$thickness}-fill{$fill01}.png");
        $image->save($filename);
        $this->assertImageEquals(IMAGINE_TEST_FIXTURESFOLDER . "/drawer/rectangle/thinkness{$thickness}-fill{$fill01}.png", $filename, '', 0.12, $imagine);
    }

    /**
     * @dataProvider thicknessAndFillProvider
     *
     * @param bool $fill
     * @param int $thickness
     */
    public function testPolygon($thickness, $fill)
    {
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(25, 25), $this->getColor('fff'));
        $size = $image->getSize();
        $fill01 = $fill ? 1 : 0;
        $drawer = $image->draw();
        $this->assertSame($drawer, $drawer->polygon(
            array(
                new Point($size->getWidth() / 2, 5),
                new Point($size->getWidth() - 5, $size->getHeight() - 5),
                new Point(5, $size->getHeight() - 5),
            ),
            $this->getColor('f00'),
            $fill,
            $thickness
        ));
        $filename = $this->getTemporaryFilename("thinkness{$thickness}-fill{$fill01}.png");
        $image->save($filename);
        $this->assertImageEquals(IMAGINE_TEST_FIXTURESFOLDER . "/drawer/polygon/thinkness{$thickness}-fill{$fill01}.png", $filename, '', 0.154, $imagine);
    }

    public function testText()
    {
        if (!$this->isFontTestSupported()) {
            $this->markTestSkipped('This install does not support font tests');
        }
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(60, 60), $this->getColor('fff'));
        $drawer = $image->draw();
        $this->assertSame($drawer, $drawer->text(
            'test',
            $imagine->font(IMAGINE_TEST_FIXTURESFOLDER . '/font/Arial.ttf', 12, $this->getColor('f00')),
            new Point(3, 3),
            45
        ));
        $filename = $this->getTemporaryFilename('.png');
        $image->save($filename);
        $this->assertImageEquals(IMAGINE_TEST_FIXTURESFOLDER . '/drawer/text/text.png', $filename, '', 0.059, $imagine);
    }

    public function testDrawASmileyFace()
    {
        $imagine = $this->getImagine();

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('000'));

        $canvas->draw()
            ->chord(new Point(200, 200), new Box(200, 150), 0, 180, $this->getColor('fff'), false)
            ->ellipse(new Point(125, 100), new Box(50, 50), $this->getColor('fff'))
            ->ellipse(new Point(275, 100), new Box(50, 50), $this->getColor('fff'), true);

        $filename = $this->getTemporaryFilename('.png');
        $canvas->save($filename);

        $this->assertFileExists($filename);
    }

    public function testText2()
    {
        if (!$this->isFontTestSupported()) {
            $this->markTestSkipped('This install does not support font tests');
        }

        $path = IMAGINE_TEST_FIXTURESFOLDER . '/font/Arial.ttf';
        $black = $this->getColor('000');
        $file36 = $this->getTemporaryFilename('36.png');
        $file24 = $this->getTemporaryFilename('24.png');
        $file18 = $this->getTemporaryFilename('18.png');
        $file12 = $this->getTemporaryFilename('12.png');

        $imagine = $this->getImagine();
        $canvas = $imagine->create(new Box(400, 300), $this->getColor('fff'));
        $font = $imagine->font($path, 36, $black);

        $canvas->draw()
            ->text('Bulat', $font, new Point(0, 0), 135);

        $canvas->save($file36);

        unset($canvas);

        $this->assertFileExists($file36);

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('fff'));
        $font = $imagine->font($path, 24, $black);

        $canvas->draw()
            ->text('Bulat', $font, new Point(24, 24));

        $canvas->save($file24);

        unset($canvas);

        $this->assertFileExists($file24);

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('fff'));
        $font = $imagine->font($path, 18, $black);

        $canvas->draw()
            ->text('Bulat', $font, new Point(18, 18));

        $canvas->save($file18);

        unset($canvas);

        $this->assertFileExists($file18);

        $canvas = $imagine->create(new Box(400, 300), $this->getColor('fff'));
        $font = $imagine->font($path, 12, $black);

        $canvas->draw()
            ->text('Bulat', $font, new Point(12, 12));

        $canvas->save($file12);

        unset($canvas);

        $this->assertFileExists($file12);
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
