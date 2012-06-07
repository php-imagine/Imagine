<?php

namespace Imagine\Image\Palette;

use Imagine\Test\ImagineTestCase;
use Imagine\Image\Palette\Color\ColorInterface;

abstract class AbstractPaletteTest extends ImagineTestCase
{
    /**
     * @dataProvider provideColorAndAlphaTuples
     */
    public function testColor($expected, $color, $alpha)
    {
        $result = $this->getPalette()->color($color, $alpha);
        $this->assertInstanceOf('Imagine\Image\Palette\Color\ColorInterface', $result);
        $this->assertEquals((string) $expected, (string) $result);
    }

    /**
     * @dataProvider provideColorsForBlending
     */
    public function testBlend($expected, $color1, $color2, $amount)
    {
        $result = $this->getPalette()->blend($color1, $color2, $amount);
        $this->assertInstanceOf('Imagine\Image\Palette\Color\ColorInterface', $result);
        $this->assertEquals((string) $expected, (string) $result);
    }

    public function testUseProfile()
    {
        $this->getMock('Imagine\Image\ProfileInterface');

        $palette = $this->getPalette();

        $new = $this->getMock('Imagine\Image\ProfileInterface');
        $palette->useProfile($new);

        $this->assertEquals($new, $palette->profile());

    }

    public function testProfile()
    {
        $this->assertInstanceOf('Imagine\Image\ProfileInterface', $this->getPalette()->profile());
    }

    public function testName()
    {
        $this->assertInternalType('string', $this->getPalette()->name());
    }

    public function testPixelDefinition()
    {
        $this->assertInternalType('array', $this->getPalette()->pixelDefinition());

        $available = array(
            ColorInterface::COLOR_RED,
            ColorInterface::COLOR_GREEN,
            ColorInterface::COLOR_BLUE,
            ColorInterface::COLOR_CYAN,
            ColorInterface::COLOR_MAGENTA,
            ColorInterface::COLOR_YELLOW,
            ColorInterface::COLOR_KEYLINE,
        );

        foreach ($this->getPalette()->pixelDefinition() as $color) {
            $this->assertTrue(in_array($color, $available));
        }
    }

    public function testSupportsAlpha()
    {
        $this->assertInternalType('boolean', $this->getPalette()->supportsAlpha());
    }

    abstract public function provideColorAndAlphaTuples();

    abstract public function provideColorsForBlending();

    /**
     * @return PaletteInterface
     */
    abstract protected function getPalette();
}
