<?php

namespace Imagine\Imagick;

use Imagine\AbstractDrawerTest;

class DrawerTest extends AbstractDrawerTest
{
    protected function setUp()
    {
        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
        }
    }

	protected function getImagine()
    {
        return new Imagine();
    }
}
