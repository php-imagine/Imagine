<?php

namespace Imagine\GD\Command;

require_once 'tests/Imagine/TestInit.php';

class CanvasResizeTest extends \PHPUnit_Framework_TestCase
{
    const IMG_GOOGLE = 'tests/fixtures/google_275x95.png';

    protected $image;

    public function setUp()
    {
        parent::setUp();
        $this->image = new \Imagine\Image(self::IMG_GOOGLE);
    }

    public function testCanvasResizeModeCenter()
    {
        $oldResource = $this->image->getResource();
        $this->assertTrue(is_resource($oldResource));

        $command = new CanvasResize(200, 200);
        $this->assertNotEquals(200, $this->image->getWidth());
        $this->assertNotEquals(200, $this->image->getHeight());

        $command->process($this->image);
        $this->assertEquals(200, $this->image->getWidth());
        $this->assertEquals(200, $this->image->getHeight());

        $newResource = $this->image->getResource();
        $this->assertTrue(is_resource($newResource));
        $this->assertFalse(is_resource($oldResource));
    }
}
