<?php

namespace Imagine\GD\Command;

require_once 'tests/Imagine/TestInit.php';

class CropTest extends \PHPUnit_Framework_TestCase
{
    const IMG_GOOGLE = 'tests/fixtures/google_275x95.png';

    protected $image;

    public function setUp()
    {
        parent::setUp();
        $this->image = new \Imagine\Image(self::IMG_GOOGLE);
    }

    public function testCrop()
    {
        $oldResource = $this->image->getResource();
        $this->assertTrue(is_resource($oldResource));

        $command = new Crop(0, 0, 50, 50);
        $this->assertNotEquals(50, $this->image->getWidth());
        $this->assertNotEquals(50, $this->image->getHeight());

        $command->process($this->image);
        $this->assertEquals(50, $this->image->getWidth());
        $this->assertEquals(50, $this->image->getHeight());

        $newResource = $this->image->getResource();
        $this->assertTrue(is_resource($newResource));
        $this->assertFalse(is_resource($oldResource));
    }
}
