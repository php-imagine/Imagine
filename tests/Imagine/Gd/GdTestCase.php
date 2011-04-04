<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

abstract class GdTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Imagine\Gd\ResourceInterface
     */
    protected function getResource()
    {
        return $this->getMock('Imagine\Gd\ResourceInterface');
    }

    /**
     * @return Imagine\Gd\GdInterface
     */
    protected function getGd()
    {
        return $this->getMock('Imagine\Gd\GdInterface');
    }
}
