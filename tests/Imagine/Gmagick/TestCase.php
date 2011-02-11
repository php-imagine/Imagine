<?php

namespace Imagine\Gmagick;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick is not installed');
        }
    }
}
