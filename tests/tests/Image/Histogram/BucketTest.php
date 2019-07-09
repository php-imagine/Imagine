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

use Imagine\Image\Histogram\Bucket;
use Imagine\Image\Histogram\Range;
use Imagine\Test\ImagineTestCase;

class BucketTest extends ImagineTestCase
{
    private $bucket;

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\ImagineTestCaseBase::setUpBase()
     */
    protected function setUpBase()
    {
        $this->bucket = new Bucket(new Range(0, 63));
        $this->assertInstanceOf('Countable', $this->bucket);
    }

    /**
     * @dataProvider getCountAndValues
     *
     * @param int $count
     * @param array $values
     */
    public function testShouldOnlyRegisterValuesInRange($count, array $values)
    {
        foreach ($values as $value) {
            $this->bucket->add($value);
        }

        $this->assertEquals($count, $this->bucket->count());
    }

    public function getCountAndValues()
    {
        return array(
            array(3, array(12, 123, 232, 142, 152, 172, 93, 35, 44)),
            array(6, array(12, 123, 23, 14, 152, 17, 93, 35, 44)),
            array(8, array(12, 12, 12, 23, 14, 152, 17, 93, 35, 44)),
            array(0, array(121, 123, 234, 145, 152, 176, 93, 135, 144)),
        );
    }
}
