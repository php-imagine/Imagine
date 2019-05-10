<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Gd;

use Imagine\Gd\Imagine;
use Imagine\Test\Image\AbstractImagineTest;

/**
 * @group gd
 */
class ImagineTest extends AbstractImagineTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
    }

    public function testShouldOpenAWebpImage()
    {
        // skip if not supported
        if (function_exists('imagecreatefromwebp')) {
            $source = IMAGINE_TEST_FIXTURESFOLDER . '/google.webp';
            $factory = $this->getImagine();
            $image = $factory->open($source);
            $size = $image->getSize();

            $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
            $this->assertEquals(550, $size->getWidth());
            $this->assertEquals(368, $size->getHeight());

            $metadata = $image->metadata();

            $this->assertEquals($source, $metadata['uri']);
            $this->assertEquals(realpath($source), $metadata['filepath']);
        }
    }

    protected function getImagine()
    {
        return new Imagine();
    }
}
