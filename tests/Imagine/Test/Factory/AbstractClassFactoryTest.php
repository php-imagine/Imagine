<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Factory;

use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Test\ImagineTestCase;

abstract class AbstractClassFactoryTest extends ImagineTestCase
{
    public function testClassFactoryIsForwarded()
    {
        $imagine = $this->getImagine();
        $image = $imagine->create(new Box(1, 1));
        $this->assertSame($imagine->getClassFactory(), $image->getClassFactory());
        $palette = new RGB();
        if ($this->canTestFont()) {
            $font = $imagine->font(__FILE__, 1, $palette->color('#000000'));
            $this->assertSame($imagine->getClassFactory(), $font->getClassFactory());
        }
    }

    /**
     * @return \Imagine\Image\ImagineInterface
     */
    abstract protected function getImagine();

    /**
     * @return bool
     */
    protected function canTestFont()
    {
        return true;
    }
}
