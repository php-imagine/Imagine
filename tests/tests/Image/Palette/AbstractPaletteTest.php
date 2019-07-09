<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Image\Palette;

use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Test\ImagineTestCase;

abstract class AbstractPaletteTest extends ImagineTestCase
{
    /**
     * @dataProvider provideColorAndAlphaTuples
     *
     * @param mixed $expected
     * @param mixed $color
     * @param mixed $alpha
     */
    public function testColor($expected, $color, $alpha)
    {
        $result = $this->getPalette()->color($color, $alpha);
        $this->assertInstanceOf('Imagine\Image\Palette\Color\ColorInterface', $result);
        $this->assertEquals((string) $expected, (string) $result);
    }

    /**
     * @dataProvider provideColorAndAlpha
     *
     * @param mixed $color
     * @param mixed $alpha
     */
    public function testColorIsCached($color, $alpha)
    {
        $this->assertSame($this->getPalette()->color($color, $alpha), $this->getPalette()->color($color, $alpha));
    }

    /**
     * @dataProvider provideColorAndAlpha
     *
     * @param mixed $color
     * @param mixed $alpha
     */
    public function testColorWithDifferentAlphasAreNotSame($color, $alpha)
    {
        $this->assertNotSame($this->getPalette()->color($color, 2), $this->getPalette()->color($color, 0));
    }

    /**
     * @dataProvider provideColorsForBlending
     *
     * @param mixed $expected
     * @param mixed $color1
     * @param mixed $color2
     * @param mixed $amount
     */
    public function testBlend($expected, $color1, $color2, $amount)
    {
        $result = $this->getPalette()->blend($color1, $color2, $amount);
        $this->assertInstanceOf('Imagine\Image\Palette\Color\ColorInterface', $result);
        $this->assertEquals((string) $expected, (string) $result);
    }

    public function testUseProfile()
    {
        $this->getMockBuilder('Imagine\Image\ProfileInterface')->getMock();

        $palette = $this->getPalette();

        $new = $this->getMockBuilder('Imagine\Image\ProfileInterface')->getMock();
        $palette->useProfile($new);

        $this->assertEquals($new, $palette->profile());
    }

    public function testProfile()
    {
        $this->assertInstanceOf('Imagine\Image\ProfileInterface', $this->getPalette()->profile());
    }

    public function testName()
    {
        $this->assertPHPType('string', $this->getPalette()->name());
    }

    public function testPixelDefinition()
    {
        $this->assertPHPType('array', $this->getPalette()->pixelDefinition());

        $available = array(
            ColorInterface::COLOR_RED,
            ColorInterface::COLOR_GREEN,
            ColorInterface::COLOR_BLUE,
            ColorInterface::COLOR_CYAN,
            ColorInterface::COLOR_MAGENTA,
            ColorInterface::COLOR_YELLOW,
            ColorInterface::COLOR_KEYLINE,
            ColorInterface::COLOR_GRAY,
        );

        foreach ($this->getPalette()->pixelDefinition() as $color) {
            $this->assertTrue(in_array($color, $available));
        }
    }

    public function testSupportsAlpha()
    {
        $this->assertPHPType('boolean', $this->getPalette()->supportsAlpha());
    }

    abstract public function provideColorAndAlphaTuples();

    abstract public function provideColorsForBlending();

    /**
     * @return \Imagine\Image\Palette\PaletteInterface
     */
    abstract protected function getPalette();
}
