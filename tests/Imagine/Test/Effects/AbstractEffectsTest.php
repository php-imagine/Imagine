<?php

namespace Imagine\Test\Effects;

use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Image\ImagineInterface;

abstract class AbstractEffectsTest extends \PHPUnit_Framework_TestCase
{

    public function testNegate()
    {
        $imagine = $this->getImagine();

        $image = $imagine->create(new Box(20, 20), new Color('ff0'));
        $image->effects()
            ->negative();

        $this->assertEquals('#0000ff', (string) $image->getColorAt(new Point(10, 10)));

        $image->effects()
            ->negative();

        $this->assertEquals('#ffff00', (string) $image->getColorAt(new Point(10, 10)));
    }

    public function testGamma()
    {
        $imagine = $this->getImagine();

        $r = 20;
        $g = 90;
        $b = 240;

        $image = $imagine->create(new Box(20, 20), new Color(array($r, $g, $b)));
        $image->effects()
            ->gamma(1.2);

        $pixel = $image->getColorAt(new Point(10, 10));

        $this->assertNotEquals($r, $pixel->getRed());
        $this->assertNotEquals($g, $pixel->getGreen());
        $this->assertNotEquals($b, $pixel->getBlue());
    }

    public function testGrayscale()
    {
        $imagine = $this->getImagine();

        $r = 20;
        $g = 90;
        $b = 240;

        $image = $imagine->create(new Box(20, 20), new Color(array($r, $g, $b)));
        $image->effects()
            ->grayscale();

        $pixel = $image->getColorAt(new Point(10, 10));

        $this->assertEquals('#565656', (string) $pixel);

        $greyR = (int) $pixel->getRed();
        $greyG = (int) $pixel->getGreen();
        $greyB = (int) $pixel->getBlue();

        $this->assertEquals($greyR, (int) 86);
        $this->assertEquals($greyR, $greyG);
        $this->assertEquals($greyR, $greyB);
        $this->assertEquals($greyG, $greyB);
    }

    public function testColorize()
    {
        $imagine = $this->getImagine();

        $blue = new Color('#0000FF');

        $image = $imagine->create(new Box(15, 15), new Color('000'));
        $image->effects()
            ->colorize($blue);

        $pixel = $image->getColorAt(new Point(10, 10));

        $this->assertEquals((string) $blue, (string) $pixel);

        $this->assertEquals($blue->getRed(), $pixel->getRed());
        $this->assertEquals($blue->getGreen(), $pixel->getGreen());
        $this->assertEquals($blue->getBlue(), $pixel->getBlue());
    }

    /**
     * @return ImagineInterface
     */
    abstract protected function getImagine();
}
