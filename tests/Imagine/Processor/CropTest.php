<?php

namespace Imagine\Processor;

use Imagine\StandardImage;

require_once 'tests/Imagine/TestInit.php';

class CropTest extends \PHPUnit_Framework_TestCase {

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

    public function testCrop() {
        $this->command = new Crop(0, 0, 50, 50);
        $this->assertEquals(275, $this->image->getWidth());
        $this->assertEquals(95, $this->image->getHeight());
        $this->command->process($this->image);
        $this->assertEquals(50, $this->image->getWidth());
        $this->assertEquals(50, $this->image->getHeight());
        $this->command->restore($this->image);
        $this->assertEquals(275, $this->image->getWidth());
        $this->assertEquals(95, $this->image->getHeight());
    }

}
