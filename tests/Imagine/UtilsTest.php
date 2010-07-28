<?php

namespace Imagine;

require_once 'tests/Imagine/TestInit.php';

class UtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorInitialization()
    {
        $this->assertEquals(array(1920, 1440), Utils::getBoxForAspectRatio(4/3, 1920, 1200, true), '4:3 around 1920x1200');
        $this->assertEquals(array(1600, 1200), Utils::getBoxForAspectRatio(4/3, 1920, 1200, false), '4:3 within 1920x1200');
        $this->assertEquals(array(1920, 1200), Utils::getBoxForAspectRatio(16/10, 1600, 1200, true), '16:10 around 1600x1200');
        $this->assertEquals(array(1600, 1000), Utils::getBoxForAspectRatio(16/10, 1600, 1200, false), '16:10 within 1600x1200');
    }
}
