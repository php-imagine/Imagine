<?php

namespace Imagine\Test\Issues;

use Imagine\Exception\RuntimeException;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

/**
 * @group ext-gd
 */
class Issue17Test extends \PHPUnit\Framework\TestCase
{
    private function getImagine()
    {
        try {
            $imagine = new Imagine();
        } catch (RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        return $imagine;
    }

    public function testShouldResize()
    {
        $size = new Box(100, 10);
        $imagine = $this->getImagine();

        $imagine->open('tests/Imagine/Fixtures/large.jpg')
            ->thumbnail($size, ImageInterface::THUMBNAIL_OUTBOUND)
            ->save('tests/Imagine/Fixtures/resized.jpg');

        $this->assertFileExists('tests/Imagine/Fixtures/resized.jpg');
        $this->assertEquals(
            $size,
            $imagine->open('tests/Imagine/Fixtures/resized.jpg')->getSize()
        );

        unlink('tests/Imagine/Fixtures/resized.jpg');
    }
}
