<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Functional;

use Imagine\Exception\RuntimeException;
use Imagine\Gd\Imagine;
use Imagine\Image\Point;
use Imagine\Test\ImagineTestCase;

/**
 * @group gd
 */
class GdTransparentGifHandlingTest extends ImagineTestCase
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

    /**
     * @doesNotPerformAssertions
     */
    public function testShouldResize()
    {
        $imagine = $this->getImagine();
        $new = $this->getTemporaryFilename('.jpeg');

        $image = $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/xparent.gif');
        $size = $image->getSize()->scale(0.5);

        $image
            ->resize($size)
        ;

        $imagine
            ->create($size)
            ->paste($image, new Point(0, 0))
            ->save($new)
        ;
    }
}
