<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Filter\Advanced;

use Imagine\Filter\Advanced\Neighborhood;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Test\Filter\FilterTestCase;
use Imagine\Utils\Matrix;

class NeighborhoodTest extends FilterTestCase
{
    /**
     * @dataProvider dataProviderForTestDoesNotRunOutOfBoundAndCalculatesCorrectColors
     *
     * @param \Imagine\Utils\Matrix $matrix
     * @param \Imagine\Image\ImageInterface $image
     */
    public function testDoesNotRunOutOfBoundAndCalculatesCorrectColors($matrix, $image)
    {
        $neighborhood = new Neighborhood($matrix);
        $neighborhood->apply($image);
    }

    public function dataProviderForTestDoesNotRunOutOfBoundAndCalculatesCorrectColors()
    {
        $tests = array();
        {
            $rgb = new RGB();
            $matrix = new Matrix(3, 3, array(
                0, 1, 0,
                1, -4, 1,
                0, 1, 0,
            ));

            $image = $this->getImage();
            $image
                ->expects($this->any())
                ->method('palette')
                ->will($this->returnValue($rgb))
            ;

            $drawer = $this->getDrawer();
            $expectedDraw = new Matrix(3, 3, array(
                array(168, 36, 255), array(0, 0, 0), array(197, 255, 255),
                array(180, 136, 0), array(0, 0, 0), array(86, 255, 56),
                array(0, 0, 16), array(195, 251, 58), array(0, 0, 0),
            ));

            $i = 0;
            for ($y = 0; $y < 3; $y++) {
                for ($x = 0; $x < 3; $x++) {
                    $drawer
                        ->expects($this->at($i++))
                        ->method('dot')
                        ->with(new Point($x + 1, $y + 1), $rgb->color($expectedDraw->getElementAt($x, $y)))
                    ;
                }
            }

            $image
                ->expects($this->any())
                ->method('draw')
                ->will($this->returnValue($drawer))
            ;

            $tests[] = array($matrix, $image);
        }

        return $tests;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Filter\FilterTestCase::getImage()
     */
    public function getImage()
    {
        $rgb = new RGB();

        $tmpMatrix = new Matrix(5, 5, array(
            array(12, 12, 42), array(24, 23, 32), array(75, 35, 81), array(43, 98, 128), array(98, 244, 200),
            array(205, 215, 55), array(34, 85, 12), array(53, 74, 99), array(1, 2, 3), array(99, 99, 124),
            array(45, 98, 156), array(22, 64, 159), array(65, 155, 99), array(6, 4, 66), array(22, 55, 144),
            array(66, 42, 67), array(124, 54, 86), array(12, 21, 64), array(22, 63, 74), array(1, 5, 7),
            array(53, 123, 54), array(160, 80, 70), array(32, 63, 55), array(43, 43, 63), array(254, 235, 122),
        ));

        $image = parent::getImage();

        $image
            ->expects($this->exactly(2))
            ->method('getSize')
            ->will($this->returnValue(new Box(5, 5)))
        ;

        $i = 2;
        for ($y = 0; $y < 5; $y++) {
            for ($x = 0; $x < 5; $x++) {
                $element = $tmpMatrix->getElementAt($x, $y);

                $color = $rgb->color($element);

                $image
                    ->expects($this->at($i++))
                    ->method('getColorAt')
                    ->with(new Point($x, $y))
                    ->will($this->returnValue($color))
                ;
            }
        }

        return $image;
    }
}
