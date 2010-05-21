<?php

namespace Imagine;

require_once 'tests/Imagine/TestInit.php';

class StandardImageTest extends \PHPUnit_Framework_TestCase {
    public function testInitializeImage() {
        $image = new StandardImage('tests/fixtures/logo1w.png');

        $this->assertTrue($image instanceof Image);
        $this->assertEquals('logo1w', $image->getName());
        $this->assertEquals(realpath('tests/fixtures/logo1w.png'), $image->getPath());
        $this->assertEquals(\IMAGETYPE_PNG, $image->getType());
        $this->assertEquals(file_get_contents('tests/fixtures/logo1w.png'), $image->getContent());
        $this->assertEquals('image/png', $image->getContentType());
    }
}
