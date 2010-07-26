<?php

namespace Imagine;

require_once 'tests/Imagine/TestInit.php';

class ImageTest extends \PHPUnit_Framework_TestCase
{
    const IMG_GOOGLE = 'tests/fixtures/google_275x95.png';
    const IMG_PHP = 'tests/fixtures/php_120x67.gif';

    protected $image;

    public function setUp()
    {
        parent::setUp();
        $this->image = new Image(self::IMG_GOOGLE);
    }

    public function testConstructorInitialization()
    {
        $this->assertTrue($this->image instanceof Image);
        $this->assertEquals(self::IMG_GOOGLE, $this->image->getPath());
        $this->assertEquals(275, $this->image->getWidth());
        $this->assertEquals(95, $this->image->getHeight());
        $this->assertEquals(\IMAGETYPE_PNG, $this->image->getType());
        $this->assertEquals('image/png', $this->image->getMimeType());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorException()
    {
        $image = new Image('not_an_image_file');
    }

    public function testGetResource()
    {
        $this->assertTrue(is_resource($this->image->getResource()));
    }

    public function testSetResource()
    {
        $oldResource = $this->image->getResource();
        $newResource = imagecreatefromgif(self::IMG_PHP);

        $this->image->setResource($newResource);
        $this->assertTrue(is_resource($this->image->getResource()));
        $this->assertEquals(120, $this->image->getWidth());
        $this->assertEquals(67, $this->image->getHeight());
        $this->assertFalse(is_resource($oldResource));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetResourceException()
    {
        $this->image->setResource('not_a_resource');
    }
}
