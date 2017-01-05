<?php
namespace Imagine\Test\Draw;

use Imagine\Draw\LineStyle;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;

class LineStyleTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaultValues()
    {
        $color = new RGB(new RGBPalette(), array(0, 128, 0), 100);
        $style = new LineStyle($color);

        $this->assertEquals($color, $style->getColor());
        $this->assertEquals(LineStyle::LINE_SOLID, $style->getStyle());
        $this->assertEquals(1, $style->getThickness());
        $this->assertEquals(1.0, $style->getSpacing());
    }

    public function testLineStyle()
    {
        $color = new RGB(new RGBPalette(), array(0, 128, 0), 100);
        $style = new LineStyle($color, LineStyle::LINE_DASHED, 10, 12.5);

        $this->assertEquals($color, $style->getColor());
        $this->assertEquals(LineStyle::LINE_DASHED, $style->getStyle());
        $this->assertEquals(10, $style->getThickness());
        $this->assertEquals(12.5, $style->getSpacing());
    }
}
