<?php
namespace Imagine;

require_once '../lib/Image.php';
require_once '../lib/ImageManager.php';
require_once '../lib/StandardImage.php';
require_once '../lib/StandardImageManager.php';

class FunctionalTest extends \PHPUnit_Framework_TestCase {
    
    public function testFetchImage() {
        $imageManager = new StandardImageManager();
        $image = $imageManager->fetchImage('fixtures/logo1w.png');
        
        $this->assertTrue($image instanceof Image);
        $this->assertEquals('logo1w', $image->getName());
        $this->assertEquals(realpath('fixtures/logo1w.png'), $image->getPath());
        $this->assertEquals(\IMAGETYPE_PNG, $image->getType());
        $this->assertEquals(file_get_contents('fixtures/logo1w.png'), $image->getContent());
        $this->assertEquals('image/png', $image->getContentType());
    }
    
    public function testDeleteSaveImage() {
        $imageManager = new StandardImageManager();
        $image = $imageManager->fetchImage('fixtures/logo1w.png');
        
        $imageManager->delete($image);
        $this->assertFalse(file_exists('fixtures/logo1w.png'));
        $imageManager->save($image);
        $this->assertTrue(file_exists('fixtures/logo1w.png'));
    }
    
    public function testResizeImage() {
        $imageManager = new StandardImageManager();
        $image = $imageManager->fetchImage('fixtures/logo1w.png');
        $image->resize(40, 40);
        $this->assertEquals(40, $image->getWidth());
        $this->assertEquals(40, $image->getHeight());
        $image->setName('40x40logo1w');
        $imageManager->save($image);
        unset($image);
        $image = $imageManager->fetchImage('fixtures/40x40logo1w.png');
        $this->assertEquals(40, $image->getWidth());
        $this->assertEquals(40, $image->getHeight());
        $imageManager->delete($image);
    }
    
    public function testCropImage() {
        $imageManager = new StandardImageManager();
        $image = $imageManager->fetchImage('fixtures/logo1w.png');
        $image->crop(20, 20, 200, 20);
        $this->assertEquals(200, $image->getWidth());
        $this->assertEquals(20, $image->getHeight());
        $image->setName('200x20logo1w');
        $imageManager->save($image);
        unset($image);
        $image = $imageManager->fetchImage('fixtures/200x20logo1w.png');
        $this->assertEquals(200, $image->getWidth());
        $this->assertEquals(20, $image->getHeight());
        $imageManager->delete($image);
    }
}