<?php

namespace Imagine;

require_once 'tests/Imagine/TestInit.php';

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testAddsCommands()
    {
        $processor = $this->getMock('Imagine\Processor', array('addCommand'));
        $processor
            ->expects($this->exactly(2))
            ->method('addCommand')
            // Mock the return value to preserve the fluent interface
            ->will($this->returnValue($processor));
        $processor
            ->resize(50, 50)
            ->crop(5, 5, 40, 40);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testAddCommandException()
    {
        $processor = new Processor();
        $processor->thisIsAnInvalidCommand(1, 2, 3);
    }
}