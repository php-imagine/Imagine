<?php

namespace Imagine\Gd;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
    }
}