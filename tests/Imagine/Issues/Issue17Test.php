<?php

namespace Imagine\Issues;

use Imagine\ImageInterface;
use Imagine\Image\Box;
use Imagine\Gd\Imagine;

class Issue17Test extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
    }

    public function testShouldResize()
    {
        $size    = new Box(100, 10);
        $imagine = new Imagine();

        $imagine->open('tests/Imagine/Fixtures/large.jpg')
            ->thumbnail($size, ImageInterface::THUMBNAIL_OUTBOUND)
            ->save('tests/Imagine/Fixtures/resized.jpg');

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/resized.jpg'));
        $this->assertEquals(
            $size,
            $imagine->open('tests/Imagine/Fixtures/resized.jpg')->getSize()
        );

        unlink('tests/Imagine/Fixtures/resized.jpg');
    }
}
