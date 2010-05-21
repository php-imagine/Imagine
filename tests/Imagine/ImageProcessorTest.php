<?php

namespace Imagine;

require_once 'lib/Imagine/Image.php';
require_once 'lib/Imagine/ImageManager.php';
require_once 'lib/Imagine/StandardImage.php';
require_once 'lib/Imagine/StandardImageManager.php';
require_once 'lib/Imagine/ImageProcessor.php';
require_once 'lib/Imagine/Processor/ProcessCommand.php';
require_once 'lib/Imagine/Processor/SetSizeCommand.php';

class ImageProcessorTest extends \PHPUnit_Framework_TestCase {
	
	protected $processor;
	protected $image;
	
	public function setUp() {
		$this->image = new StandardImage('tests/fixtures/logo1w.png');
		$this->image->setSize(40, 40);
		$this->processor = new ImageProcessor($this->image);
	}
	
	public function tearDown() {
		unset ($this->processor, $this->image);
	}

	public function testConstructorSetsImage() {
		$this->assertEquals($this->image, $this->processor->getImage());
	}

	public function testSetSize() {
		$oldContent = $this->image->getContent();
		$this->processor->setSize(80, 35);
		$this->assertEquals(40, $this->image->getHeight());
		$this->assertEquals(40, $this->image->getWidth());

		$this->processor->process();
		$this->assertEquals(80, $this->image->getWidth());
		$this->assertEquals(35, $this->image->getHeight());
		$this->assertNotEquals($oldContent, $this->image->getContent());
	}

	public function testKeepRatio() {
		$this->processor->keepRatio(true);
		$this->assertTrue($this->processor->keepRatio());
	}
	
}