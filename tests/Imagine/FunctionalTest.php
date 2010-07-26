<?php
namespace Imagine;

require_once 'tests/Imagine/TestInit.php';

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    const IMG_GOOGLE = 'tests/fixtures/google_275x95.png';

    protected $tmpFile;

    public function setUp()
    {
        parent::setUp();
        if (false === ($this->tmpFile = tempnam(sys_get_temp_dir(), 'img'))) {
            $this->markTestSkipped('Cannot create temporary file');
        }
    }

    public function tearDown()
    {
        unlink($this->tmpFile);
        parent::tearDown();
    }

    public function testResizeImage()
    {
        $image = new Image(self::IMG_GOOGLE);

        $processor = new Processor();
        $processor
            ->resize(40, 40)
            ->save($this->tmpFile)
            ->process($image);
        unset($image);

        $image = new Image($this->tmpFile);
        $this->assertEquals(40, $image->getWidth());
        $this->assertEquals(40, $image->getHeight());
        unset($image);

        $image = new Image(self::IMG_GOOGLE);
        $this->assertEquals(275, $image->getWidth());
        $this->assertEquals(95, $image->getHeight());
        unset($image);
    }

    public function testCropImage() {
        $image = new Image(self::IMG_GOOGLE);

        $processor = new Processor();
        $processor
            ->crop(20, 20, 200, 20)
            ->save($this->tmpFile)
            ->process($image);
        unset($image);

        $image = new Image($this->tmpFile);
        $this->assertEquals(200, $image->getWidth());
        $this->assertEquals(20, $image->getHeight());
        unset($image);

        $image = new Image(self::IMG_GOOGLE);
        $this->assertEquals(275, $image->getWidth());
        $this->assertEquals(95, $image->getHeight());
        unset($image);
    }
}