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

class ImagineTestCase_v1 extends ImagineTestCaseBase
{
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClassBase();
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    final public function setUp()
    {
        $this->setUpBase();
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    final public function tearDown()
    {
        $this->tearDownBase();
    }

    /**
     * @param string $expected
     * @param mixed $actual
     * @param string $message
     */
    protected function assertPHPType($expected, $actual, $message = '')
    {
        $this->assertInternalType($expected, $actual, $message);
    }
}
