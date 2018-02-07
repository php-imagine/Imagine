<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Image\Metadata;

use Imagine\Image\Metadata\MetadataBag;

class MetadataBagTest extends \PHPUnit\Framework\TestCase
{
    public function testArrayAccessImplementation()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2');
        $bag = new MetadataBag($data);

        $this->assertFalse(isset($bag['key3']));
        $this->assertTrue(isset($bag['key1']));
        $bag['key3'] = 'value3';
        $this->assertTrue(isset($bag['key3']));
        unset($bag['key3']);
        $this->assertFalse(isset($bag['key3']));
        $bag['key1'] = 'valuetest';
        $this->assertEquals('valuetest', $bag['key1']);
        $this->assertEquals('value2', $bag['key2']);
    }

    public function testIteratorAggregateImplementation()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2');
        $bag = new MetadataBag($data);

        $this->assertEquals(new \ArrayIterator($data), $bag->getIterator());
    }
}
