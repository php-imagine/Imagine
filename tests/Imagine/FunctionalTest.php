<?php
namespace Imagine;

require_once 'tests/Imagine/TestInit.php';

class FunctionalTest extends \PHPUnit_Framework_TestCase {
    
    public function testDeleteSaveImage() {
        $imageManager = new StandardImageManager();
        $image = new StandardImage('tests/fixtures/logo1w.png');
        
        $imageManager->delete($image);
        $this->assertFalse(file_exists('tests/fixtures/logo1w.png'));
        $imageManager->save($image);
        $this->assertTrue(file_exists('tests/fixtures/logo1w.png'));
    }
    
    public function testResizeImage() {
        $imageManager = new StandardImageManager();
        $image = new StandardImage('tests/fixtures/logo1w.png');
        $imageProcessor = new ImageProcessor();
        $imageProcessor->resize(40, 40);
        $imageProcessor->process($image);
        $this->assertEquals(40, $image->getWidth());
        $this->assertEquals(40, $image->getHeight());
        $image->setName('40x40logo1w');
        $imageManager->save($image);
        unset($image);
        $image = new StandardImage('tests/fixtures/40x40logo1w.png');
        $this->assertEquals(40, $image->getWidth());
        $this->assertEquals(40, $image->getHeight());
        $imageManager->delete($image);
    }
    
    public function testCropImage() {
        $imageManager = new StandardImageManager();
        $image = new StandardImage('tests/fixtures/logo1w.png');
        $imageProcessor = new ImageProcessor();
        $imageProcessor->crop(20, 20, 200, 20);
        $imageProcessor->process($image);
        $this->assertEquals(200, $image->getWidth());
        $this->assertEquals(20, $image->getHeight());
        $image->setName('200x20logo1w');
        $imageManager->save($image);
        unset($image);
        $image = new StandardImage('tests/fixtures/200x20logo1w.png');
        $this->assertEquals(200, $image->getWidth());
        $this->assertEquals(20, $image->getHeight());
        $imageManager->delete($image);
    }
}
