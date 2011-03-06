<?php

namespace Imagine\Test;

use Imagine\ImageInterface;
use Imagine\Test\Constraint\IsImageEqual;

class ImagineTestCase extends \PHPUnit_Framework_TestCase
{
    public static function assertImageEquals($expected, $actual, $message = '', $delta = 1, $buckets = 4)
    {
        $constraint = new IsImageEqual($expected, $delta, $buckets);

        self::assertThat($actual, $constraint, $message);
    }
}
