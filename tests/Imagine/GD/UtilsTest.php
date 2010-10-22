<?php

namespace Imagine\GD;

require_once 'tests/Imagine/TestInit.php';

class UtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testSaveQualityJpeg()
    {
        $this->assertEquals(20, Utils::getSaveQuality(IMAGETYPE_JPEG, 20));
        $this->assertEquals(100, Utils::getSaveQuality(IMAGETYPE_JPEG, 100));
    }

    public function testSaveQualityPng()
    {
        $this->assertEquals(7, Utils::getSaveQuality(IMAGETYPE_PNG, 20));
        $this->assertEquals(9, Utils::getSaveQuality(IMAGETYPE_PNG, 0));
        $this->assertEquals(0, Utils::getSaveQuality(IMAGETYPE_PNG, 100));
    }
}
