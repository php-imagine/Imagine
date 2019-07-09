<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Image\Histogram;

use Imagine\Image\Histogram\Range;
use Imagine\Test\ImagineTestCase;

class RangeTest extends ImagineTestCase
{
    private $start = 0;
    private $end = 63;

    /**
     * @dataProvider getExpectedResultsAndValues
     *
     * @param bool $contains
     * @param int $value
     */
    public function testShouldDetermineIfContainsValue($contains, $value)
    {
        $range = new Range($this->start, $this->end);

        $this->assertEquals($contains, $range->contains($value));
    }

    public function getExpectedResultsAndValues()
    {
        return array(
            array(true, 12),
            array(true, 0),
            array(false, 128),
            array(false, 63),
        );
    }

    public function testShouldThrowExceptionIfEndIsSmallerThanStart()
    {
        $this->isGoingToThrowException('Imagine\Exception\OutOfBoundsException');
        new Range($this->end, $this->start);
    }
}
