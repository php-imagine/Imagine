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

class ImagineTestCase_v2 extends ImagineTestCaseBase
{
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClassBase();
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    final public function setUp(): void
    {
        $this->setUpBase();
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    final public function tearDown(): void
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
        switch (strtolower($expected)) {
            case 'array':
                $this->assertIsArray($actual, $message);
                break;
            case 'boolean':
            case 'bool':
                $this->assertIsBool($actual, $message);
                break;
            case 'double':
            case 'float':
            case 'real':
                $this->assertIsFloat($actual, $message);
                break;
            case 'integer':
            case 'int':
                $this->assertIsInt($actual, $message);
                break;
            case 'null':
                $this->assertNull($actual, $message);
                break;
            case 'numeric':
                $this->assertIsNumeric($actual, $message);
                break;
            case 'object':
            case 'numeric':
                $this->assertIsObject($actual, $message);
                break;
            case 'resource':
                $this->assertIsResource($actual, $message);
                break;
            case 'string':
                $this->assertIsString($actual, $message);
                break;
            case 'scalar':
                $this->assertIsScalar($actual, $message);
                break;
            case 'callable':
                $this->assertIsCallable($actual, $message);
                break;
            case 'iterable':
                $this->assertIsIterable($actual, $message);
                break;
            default:
                throw new \Exception('Invalid type for ' . __METHOD__);
        }
    }
}
