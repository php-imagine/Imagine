<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter;

abstract class FilterTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getImage()
    {
        return $this->getMock('Imagine\\Image\\ImageInterface');
    }
}
