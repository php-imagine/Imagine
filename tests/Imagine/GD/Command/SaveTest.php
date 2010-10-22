<?php

namespace Imagine\GD\Command;

require_once 'tests/Imagine/TestInit.php';

class SaveTest extends \PHPUnit_Framework_TestCase
{
    const IMG_PNG = 'tests/fixtures/land.png';
    const IMG_JPEG = 'tests/fixtures/land.jpg';

    protected $png;
    protected $jpeg;
    protected $tmpFile;

    public function setUp()
    {
        parent::setUp();
        $this->png = new \Imagine\Image(self::IMG_PNG);
        $this->jpeg = new \Imagine\Image(self::IMG_JPEG);
        $this->tmpFile = sys_get_temp_dir().'/'.uniqid();
    }

    public function tearDown()
    {
        if(file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }

    public function testSave()
    {
        $this->assertFalse(file_exists($this->tmpFile));
        $command = new Save($this->tmpFile);
        $command->process($this->png);
        $this->assertTrue(file_exists($this->tmpFile));
    }

    public function testSaveJpegWithHighQuality()
    {
        $originalFileSize = filesize(self::IMG_JPEG);
        $command = new Save($this->tmpFile, null, array('quality' => 100));
        $command->process($this->jpeg);
        $newFileSize = filesize($this->tmpFile);
        $this->assertGreaterThanOrEqual($originalFileSize, $newFileSize);
    }

    public function testSaveJpegWithLowQuality()
    {
        $originalFileSize = filesize(self::IMG_JPEG);
        $command = new Save($this->tmpFile, null, array('quality' => 10));
        $command->process($this->jpeg);
        $newFileSize = filesize($this->tmpFile);
        $this->assertLessThanOrEqual($originalFileSize, $newFileSize);
    }

    public function testSavePngWithHighQuality()
    {
        $originalFileSize = filesize(self::IMG_PNG);
        $command = new Save($this->tmpFile, null, array('quality' => 100));
        $command->process($this->png);
        $newFileSize = filesize($this->tmpFile);
        $this->assertGreaterThanOrEqual($originalFileSize, $newFileSize);
    }

    public function testSavePngWithLowQuality()
    {
        $originalFileSize = filesize(self::IMG_PNG);
        $command = new Save($this->tmpFile, null, array('quality' => 0));
        $command->process($this->png);
        $newFileSize = filesize($this->tmpFile);
        $this->assertLessThanOrEqual($originalFileSize, $newFileSize);
    }
}
