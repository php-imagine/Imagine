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

use Imagine\Driver\InfoProvider;
use Imagine\Gd\DriverInfo;
use Imagine\Gd\Imagine;
use Imagine\Image\Point;
use Imagine\Test\ImagineTestCase;

/**
 * @group gd
 */
class GdTransparentGifHandlingTest extends ImagineTestCase implements InfoProvider
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\InfoProvider::getDriverInfo()
     */
    public static function getDriverInfo($required = true)
    {
        return DriverInfo::get($required);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testShouldResize()
    {
        $imagine = new Imagine();
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
