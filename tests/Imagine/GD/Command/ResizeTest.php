<?php

namespace Imagine\GD\Command;

require_once 'tests/Imagine/TestInit.php';

class ResizeTest extends \PHPUnit_Framework_TestCase
{
    const IMG_GOOGLE = 'tests/fixtures/google_275x95.png';

    protected $image;

    public function setUp()
    {
        parent::setUp();
        $this->image = new \Imagine\Image(self::IMG_GOOGLE);
    }

    public function testResizeNormal()
    {
        $oldResource = $this->image->getResource();
        $this->assertTrue(is_resource($oldResource));

        $command = new Resize(100, 200);
        $this->assertNotEquals(100, $this->image->getWidth());
        $this->assertNotEquals(200, $this->image->getHeight());

        $command->process($this->image);
        $this->assertEquals(100, $this->image->getWidth());
        $this->assertEquals(200, $this->image->getHeight());

        $newResource = $this->image->getResource();
        $this->assertTrue(is_resource($newResource));
        $this->assertFalse(is_resource($oldResource));
    }

    public function testResizeModeInferHeight()
    {
        $command = new Resize(55, null, Resize::INFER_HEIGHT);
        $command->process($this->image);
        $this->assertEquals(55, $this->image->getWidth());
        $this->assertEquals(19, $this->image->getHeight());
    }

    public function testResizeModeInferWidth()
    {
        $command = new Resize(null, 19, Resize::INFER_WIDTH);
        $command->process($this->image);
        $this->assertEquals(55, $this->image->getWidth());
        $this->assertEquals(19, $this->image->getHeight());
    }

    public function testResizeModeArAround()
    {
        $command = new Resize(150, 150, Resize::AR_AROUND);
        $command->process($this->image);
        $this->assertEquals(434, $this->image->getWidth());
        $this->assertEquals(150, $this->image->getHeight());
    }

    public function testResizeModeArWithin()
    {
        $command = new Resize(150, 150, Resize::AR_WITHIN);
        $command->process($this->image);
        $this->assertEquals(150, $this->image->getWidth());
        $this->assertEquals(52, $this->image->getHeight());
    }
}
