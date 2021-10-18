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

use Imagine\Driver\Info;
use Imagine\Driver\InfoProvider;
use Imagine\Image\Palette\RGB;
use Imagine\Test\Gmagick\FontTest as GmagickFontTest;
use Imagine\Test\ImagineTestCase;

abstract class AbstractFontTest extends ImagineTestCase implements InfoProvider
{
    /**
     * @return \Imagine\Image\ImagineInterface
     */
    abstract protected function getImagine();

    public function testShouldDetermineFontSize()
    {
        if (!$this->getDriverInfo()->hasFeature(Info::FEATURE_TEXTFUNCTIONS)) {
            $this->isGoingToThrowException('Imagine\Exception\NotSupportedException');
        }
        $palette = new RGB();
        $path = IMAGINE_TEST_FIXTURESFOLDER . '/font/Arial.ttf';
        $black = $palette->color('000');
        $factory = $this->getImagine();

        $this->assertBoxInRange(
            112,
            118,
            45,
            55,
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
     * @param string $text
     * @param int $maxWidth
     * @param string $expectedText
     */
    public function testFontWrapText($text, $maxWidth, $expectedText)
    {
        if (!$this->getDriverInfo()->hasFeature(Info::FEATURE_TEXTFUNCTIONS)) {
            $this->isGoingToThrowException('Imagine\Exception\NotSupportedException');
        }
        if ($this instanceof GmagickFontTest) {
            // This is needed because the Gmagick driver sometimes kills the process with
            // Magick: abort due to signal 11 (SIGSEGV) "Segmentation Fault"
            gc_disable();
        }
        $palette = new RGB();
        $font = $this->getImagine()->font(IMAGINE_TEST_FIXTURESFOLDER . '/font/Arial.ttf', 10, $palette->color('000'));
        $this->assertSame($expectedText, $font->wrapText($text, $maxWidth));
    }
}
