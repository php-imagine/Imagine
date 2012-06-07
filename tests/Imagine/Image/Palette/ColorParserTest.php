<?php

namespace Imagine\Image\Palette;

use Imagine\Test\ImagineTestCase;

class ColorParserTest extends ImagineTestCase
{
    /**
     * @dataProvider provideRGBdataToParse
     */
    public function testParseToRGB($expected, $value)
    {
        $parser = new ColorParser();

        $this->assertEquals($expected, $parser->parseToRGB($value));
    }

    /**
     * @dataProvider provideRGBdataThatFail
     * @expectedException Imagine\Exception\InvalidArgumentException
     */
    public function testParseToRGBThatFails($value)
    {
        $parser = new ColorParser();
        $parser->parseToRGB($value);
    }

    /**
     * @dataProvider provideCMYKdataToParse
     */
    public function testParseToCMYK($expected, $value)
    {
        $parser = new ColorParser();

        $this->assertEquals($expected, $parser->parseToCMYK($value));
    }

    /**
     * @dataProvider provideCMYKdataThatFail
     * @expectedException Imagine\Exception\InvalidArgumentException
     */
    public function testParseToCMYKThatFails($value)
    {
        $parser = new ColorParser();
        $parser->parseToCMYK($value);
    }

    public function provideRGBdataToParse()
    {
        return array(
            array(array(255, 255, 0), 'ff0'),
            array(array(255, 255, 0), '#ff0'),
            array(array(205, 162, 52), 'CDA234'),
            array(array(205, 162, 52), '#CDA234'),
            array(array(205, 162, 52), 13476404),
            array(array(124, 32, 125), array(124, 32, 125)),
        );
    }

    public function provideCMYKdataToParse()
    {
        return array(
            array(array(0, 0, 0, 0), 'FFFFFF'),
            array(array(0, 0, 0, 100), '000000'),
            array(array(0, 21, 75, 20), 'CDA234'),
            array(array(0, 21, 75, 20), '#CDA234'),
            array(array(0, 21, 75, 20), 'cmyk(0, 21, 75, 20)'),
            array(array(0, 21, 75, 20), 'cmyk(0,21,75,20)'),
            array(array(0, 21, 75, 20), 'cmyk(0%, 21%, 75%, 20%)'),
            array(array(0, 21, 75, 20), 'cmyk(0%,21%,75%,20%)'),
            array(array(0, 21, 75, 20), 13476404),
            array(array(100, 0, 100, 0), '#00FF00'),
            array(array(24, 32, 75, 12), array(24, 32, 75, 12)),
        );
    }

    public function provideRGBdataThatFail()
    {
        return array(
            array(array(0, 1)),
            array(array(0, 1, 0, 1, 0)),
            array('1234'),
            array('#1234'),
            array(imagecreatetruecolor(10, 10)),
        );
    }

    public function provideCMYKdataThatFail()
    {
        return array(
            array(array(0, 1)),
            array(array(0, 1, 0, 1, 0)),
            array('1234'),
            array('#1234'),
            array(imagecreatetruecolor(10, 10)),
        );
    }
}
