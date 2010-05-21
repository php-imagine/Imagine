<?php
namespace Imagine;

require_once 'tests/Imagine/TestInit.php';

class FunctionalTest extends \PHPUnit_Framework_TestCase {
    
    public function testDeleteSaveImage() {
        $image = new StandardImage('tests/fixtures/logo1w.png');
        $imageProcessor = new ImageProcessor();
        
        $imageProcessor->delete();
        $imageProcessor->process($image);
        $this->assertFalse(file_exists('tests/fixtures/logo1w.png'));
        $imageProcessor->restore($image);
        $this->assertTrue(file_exists('tests/fixtures/logo1w.png'));
    }
    
    public function testResizeImage() {
        $image = new StandardImage('tests/fixtures/logo1w.png');
        $this->assertEquals(275, $image->getWidth());
        $this->assertEquals(95, $image->getHeight());
        $imageProcessor = new ImageProcessor();
        $imageProcessor->resize(40, 40);
        $imageProcessor->process($image);
        $this->assertEquals(40, $image->getWidth());
        $this->assertEquals(40, $image->getHeight());
        $save = new Processor\SaveCommand('tests/fixtures');
        $save->process($image);
        unset($image);
        $image = new StandardImage('tests/fixtures/logo1w.png');
        $this->assertEquals(40, $image->getWidth());
        $this->assertEquals(40, $image->getHeight());
        $imageProcessor->restore($image);
        $save->process($image);
        unset($image);
        $image = new StandardImage('tests/fixtures/logo1w.png');
        $this->assertEquals(275, $image->getWidth());
        $this->assertEquals(95, $image->getHeight());
    }

    public function testCropImage() {
        $image = new StandardImage('tests/fixtures/logo1w.png');
        $imageProcessor = new ImageProcessor();
        $imageProcessor->crop(20, 20, 200, 20);
        $imageProcessor->save('tests/fixtures');
        $imageProcessor->process($image);
        $this->assertEquals(200, $image->getWidth());
        $this->assertEquals(20, $image->getHeight());
        unset($image);
        $image = new StandardImage('tests/fixtures/logo1w.png');
        $this->assertEquals(200, $image->getWidth());
        $this->assertEquals(20, $image->getHeight());
        $imageProcessor->restore($image);
        $imageProcessor->save('tests/fixtures');
        $imageProcessor->process($image);
        $this->assertEquals(275, $image->getWidth());
        $this->assertEquals(95, $image->getHeight());
        unset ($image);
        $image = new StandardImage('tests/fixtures/logo1w.png');
        $this->assertEquals(275, $image->getWidth());
        $this->assertEquals(95, $image->getHeight());
    }
}