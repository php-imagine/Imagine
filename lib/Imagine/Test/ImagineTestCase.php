<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test;

use Imagine\Test\Constraint\IsImageEqual;

class ImagineTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Asserts that two images are equal using color histogram comparison method
     *
     * @param Imagine\Image\ImageInterface $expected
     * @param Imagine\Image\ImageInterface $actual
     * @param string                       $message
     * @param float                        $delta
     * @param integer                      $buckets
     */
    public static function assertImageEquals($expected, $actual, $message = '', $delta = 0.1, $buckets = 4)
    {
        $constraint = new IsImageEqual($expected, $delta, $buckets);

        self::assertThat($actual, $constraint, $message);
    }
}
