<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Filter;

use Imagine\Test\ImagineTestCase;

abstract class FilterTestCase extends ImagineTestCase
{
    protected function getImage()
    {
        return $this->getMockBuilder('Imagine\\Image\\ImageInterface')->getMock();
    }

    protected function getImagine()
    {
        return $this->getMockBuilder('Imagine\\Image\\ImagineInterface')->getMock();
    }

    protected function getDrawer()
    {
        return $this->getMockBuilder('Imagine\\Draw\\DrawerInterface')->getMock();
    }

    protected function getPalette()
    {
        return $this->getMockBuilder('Imagine\\Image\\Palette\\PaletteInterface')->getMock();
    }

    protected function getColor()
    {
        return $this->getMockBuilder('Imagine\\Image\\Palette\\Color\\ColorInterface')->getMock();
    }
}
