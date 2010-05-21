<?php

namespace Imagine\Processor;

use Imagine\StandardImage;

require_once 'tests/Imagine/TestInit.php';

class ResizeCommandTest extends \PHPUnit_Framework_TestCase {

    protected $image;
    protected $command;

    public function setUp() {
        $this->image = new StandardImage('tests/fixtures/logo1w.png');
    }

    public function tearDown() {
        $this->assertNotEquals($this->image->getResource(),
                $this->command->getImageResource());
        unset ($this->image, $this->command);
    }

    public function testResizeNormal() {
        $this->command = new ResizeCommand(100, 200);
        $this->assertNotEquals(100, $this->image->getWidth());
        $this->assertNotEquals(200, $this->image->getHeight());
        $this->assertNull($this->command->getImageResource());
        $this->command->process($this->image);
        $this->assertEquals(100, $this->image->getWidth());
        $this->assertEquals(200, $this->image->getHeight());
        $this->assertNotNull($this->command->getImageResource());
        $this->command->restore($this->image);
        $this->assertNull($this->command->getImageResource());
        $this->assertEquals(275, $this->image->getWidth());
        $this->assertEquals(95, $this->image->getHeight());
    }

    public function testResizeWidthWithRatio() {
        $this->command = new ResizeCommand(55, true);
        $this->assertNull($this->command->getImageResource());
        $this->command->process($this->image);
        $this->assertEquals(55, $this->image->getWidth());
        $this->assertEquals(19, $this->image->getHeight());
        $this->assertNotNull($this->command->getImageResource());
        $this->command->restore($this->image);
        $this->assertNull($this->command->getImageResource());
        $this->assertEquals(275, $this->image->getWidth());
        $this->assertEquals(95, $this->image->getHeight());
    }

    public function testResizeHeightWithRatio() {
        $this->command = new ResizeCommand(true, 19);
        $this->assertNull($this->command->getImageResource());
        $this->command->process($this->image);
        $this->assertEquals(55, $this->image->getWidth());
        $this->assertEquals(19, $this->image->getHeight());
        $this->assertNotNull($this->command->getImageResource());
        $this->command->restore($this->image);
        $this->assertNull($this->command->getImageResource());
        $this->assertEquals(275, $this->image->getWidth());
        $this->assertEquals(95, $this->image->getHeight());
    }
}
