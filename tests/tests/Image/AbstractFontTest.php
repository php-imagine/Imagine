<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Image;

use Imagine\Image\Palette\RGB;
use Imagine\Test\ImagineTestCase;

abstract class AbstractFontTest extends ImagineTestCase
{
    public function testShouldDetermineFontSize()
    {
        $palette = new RGB();
        $path = IMAGINE_TEST_FIXTURESFOLDER . '/font/Arial.ttf';
        $black = $palette->color('000');
        $factory = $this->getImagine();

        $this->assertBoxInRange(
            112, 118,
            45, 55,
            $factory->font($path, 36, $black)->box('string')
        );
    }

    public function fontWrapTextProvider()
    {
        return array(
            array('', 1, ''),
            array('a', 1, 'a'),
            array("\na", 1, "\na"),
            array("a\n", 1, "a\n"),
            array('a b', 1, "a\nb"),
            array('firstword secondword thirdword', 150, "firstword secondword\nthirdword"),
        );
    }

    /**
     * @dataProvider fontWrapTextProvider
     *
     * @param mixed $text
     * @param mixed $maxWidth
     * @param mixed $expectedText
     */
    public function testFontWrapText($text, $maxWidth, $expectedText)
    {
        $palette = new RGB();
        $font = $this->getImagine()->font(IMAGINE_TEST_FIXTURESFOLDER . '/font/Arial.ttf', 10, $palette->color('000'));
        $this->assertSame($expectedText, $font->wrapText($text, $maxWidth));
    }

    /**
     * @return \Imagine\Image\ImagineInterface
     */
    abstract protected function getImagine();
}
