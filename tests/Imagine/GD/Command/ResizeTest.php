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

    public function testResizeInferHeight()
    {
        $command = new Resize(55, true);
        $command->process($this->image);
        $this->assertEquals(55, $this->image->getWidth());
        $this->assertEquals(19, $this->image->getHeight());
    }

    public function testResizeInferWidth() {
        $command = new Resize(true, 19);
        $command->process($this->image);
        $this->assertEquals(55, $this->image->getWidth());
        $this->assertEquals(19, $this->image->getHeight());
    }
}
