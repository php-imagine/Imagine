<?php

namespace Imagine;

require_once 'tests/Imagine/TestInit.php';

class ImageProcessorTest extends \PHPUnit_Framework_TestCase {
    
    protected $processor;
    protected $image;
    
    public function setUp() {
        $this->image = new StandardImage('tests/fixtures/logo1w.png');
        $this->image->setHeight(40);
        $this->image->setWidth(40);

        $this->processor = new ImageProcessor($this->image);
    }
    
    public function tearDown() {
        unset ($this->processor, $this->image);
    }

    public function testConstructorSetsImage() {
        $this->assertEquals($this->image, $this->processor->getImage());
    }

    public function testAddsCommands() {
        $processor = $this->getMock('Imagine\ImageProcessor', array('addCommand'), array($this->image));
        $processor->expects($this->exactly(2))
            ->method('addCommand');
        $processor->resize(80, 35)
                ->crop(5, 5, 45, 45);
    }
    
}