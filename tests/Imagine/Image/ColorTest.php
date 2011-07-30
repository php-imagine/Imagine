<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

class ColorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover Imagine\Image\Color::__construct
     * @cover Imagine\Image\Color::getRed
     * @cover Imagine\Image\Color::getGreen
     * @cover Imagine\Image\Color::getAlpha
     * @cover Imagine\Image\Color::__toString
     * @cover Imagine\Image\Color::isOpaque
     */
    public function testShouldSetColorToWhite()
    {
        $color = new Color('fff');

        $this->assertEquals(255, $color->getRed());
        $this->assertEquals(255, $color->getGreen());
        $this->assertEquals(255, $color->getBlue());
        $this->assertEquals(0, $color->getAlpha());

        $this->assertEquals('#ffffff', (string) $color);
        $this->assertEquals('#00ff00', (string) new Color('00ff00'));

        $this->assertTrue($color->isOpaque());
    }

    /**
     * @expectedException Imagine\Exception\InvalidArgumentException
     */
    public function testShouldThrowExceptionOnAlphaMoreThan100Percent()
    {
        new Color('fff', 200);
    }

    /**
     * @expectedException Imagine\Exception\InvalidArgumentException
     */
    public function testShouldThrowExceptionOnAlphaLessThan0Percent()
    {
        new Color('fff', -1);
    }

    /**
     * @expectedException Imagine\Exception\InvalidArgumentException
     */
    public function testShouldThrowExceptionOnColorWithMoreThanThreeValues()
    {
        new Color(array(255, 255, 255, 127));
    }

    /**
     * @expectedException Imagine\Exception\InvalidArgumentException
     */
    public function testShouldThrowExceptionOnColorWithLessThanThreeValues()
    {
        new Color(array(255, 255));
    }

    /**
     * @expectedException Imagine\Exception\InvalidArgumentException
     */
    public function testShouldThrowExceptionOnColorWithMoreThanSixCharacters()
    {
        new Color('fffffff');
    }

    /**
     * @expectedException Imagine\Exception\InvalidArgumentException
     */
    public function testShouldThrowExceptionOnColorWithLessThanSixAndMoreThanThreeCharacters()
    {
        new Color('ffff');
    }

    /**
     * @expectedException Imagine\Exception\InvalidArgumentException
     */
    public function testShouldThrowExceptionOnColorWithLessThanThreeCharacters()
    {
        new Color('ff');
    }

    /**
     * @expectedException Imagine\Exception\InvalidArgumentException
     */
    public function testShouldThrowExceptionOnColorInvalidArgument()
    {
        new Color(new \stdClass);
    }

    public function testShortColorNotationAndLongNotationProducesTheSameColor()
    {
        $this->assertEquals(new Color('fff'), new Color('ffffff'));
        $this->assertEquals(new Color('#fff'), new Color('#ffffff'));
        $this->assertEquals(new Color('#fff'), new Color('ffffff'));
        $this->assertEquals(new Color('fff'), new Color('#ffffff'));
    }

    public function testShouldAllowHexadecimalNotation()
    {
        $this->assertEquals(new Color(0xFF0000), new Color(array(255, 0, 0)));
        $this->assertEquals(new Color(0x00FF00), new Color(array(0, 255, 0)));
        $this->assertEquals(new Color(0x0000FF), new Color(array(0, 0, 255)));
    }

    public function testShouldLightenColor()
    {
        $color = new Color('000');

        $this->assertEquals(new Color('222'), $color->lighten(34));
        $this->assertEquals(new Color('fff'), $color->lighten(300));
    }

    public function testShouldDarkenColor()
    {
        $color = new Color('fff');

        $this->assertEquals(new Color('ddd'), $color->darken(34));
        $this->assertEquals(new Color('000'), $color->darken(300));
    }

    public function testShouldDissolveColor()
    {
        $color = new Color('fff');

        $this->assertEquals(new Color('fff', 50), $color->dissolve(50));
        $this->assertEquals(new Color('fff'), $color->dissolve(100)->dissolve(-100));

        $this->assertFalse($color->dissolve(1)->isOpaque());
    }
}
