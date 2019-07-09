<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Constraint;

use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Test\ImagineTestCase;

class IsBoxInRangeTest extends ImagineTestCase
{
    public function isBoxInRangeShouldFailProvider()
    {
        return array(
            array(10, 20, 30, 40, new Box(1, 1)),
            array(10, 20, 30, 40, new Box(9, 41)),
            array(1, 1, 2, 2, new Box(1, 3)),
        );
    }

    /**
     * @dataProvider isBoxInRangeShouldFailProvider
     *
     * @param int $minWidth
     * @param int $maxWidth
     * @param int $minHeight
     * @param int $maxHeight
     * @param \Imagine\Image\BoxInterface $actual
     */
    public function testIsBoxInRangeShouldFail($minWidth, $maxWidth, $minHeight, $maxHeight, BoxInterface $actual)
    {
        $this->isGoingToThrowException('PHPUnit\Framework\ExpectationFailedException');
        $this->assertBoxInRange($minWidth, $maxWidth, $minHeight, $maxHeight, $actual);
    }

    public function isBoxInRangeShouldSucceedProvider()
    {
        return array(
            array(10, 20, 30, 40, new Box(10, 30)),
            array(10, 20, 30, 40, new Box(10, 40)),
            array(10, 20, 30, 40, new Box(15, 40)),
            array(1, 1, 2, 2, new Box(1, 2)),
        );
    }

    /**
     * @dataProvider isBoxInRangeShouldSucceedProvider

     *
     * @param int $minWidth
     * @param int $maxWidth
     * @param int $minHeight
     * @param int $maxHeight
     * @param \Imagine\Image\BoxInterface $actual
     */
    public function testIsBoxInRangeShouldSucceed($minWidth, $maxWidth, $minHeight, $maxHeight, BoxInterface $actual)
    {
        $this->assertBoxInRange($minWidth, $maxWidth, $minHeight, $maxHeight, $actual);
    }
}
