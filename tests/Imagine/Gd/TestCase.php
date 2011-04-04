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

abstract class TestCase extends \PHPUnit_Framework_TestCase
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

    /**
     * Sets transparency related expectations
     *
     * @param PHPUnit_Framework_MockObject_MockObject $resource
     */
    protected function expectTransparencyToBeEnabled(\PHPUnit_Framework_MockObject_MockObject $resource)
    {
        $this->expectSaveAlpha($resource);
        $this->expectAlphaBlending($resource);
    }

    /**
     * @param PHPUnit_Framework_MockObject_MockObject $resource
     * @param boolean                                 $result
     */
    protected function expectAlphaBlending(\PHPUnit_Framework_MockObject_MockObject $resource, $result = true)
    {
        $resource->expects($this->once())
            ->method('alphablending')
            ->with(false)
            ->will($this->returnValue($result));
    }

    /**
     * @param PHPUnit_Framework_MockObject_MockObject $resource
     * @param boolean                                 $result
     */
    protected function expectSaveAlpha(\PHPUnit_Framework_MockObject_MockObject $resource, $result = true)
    {
        $resource->expects($this->once())
            ->method('savealpha')
            ->with(true)
            ->will($this->returnValue($result));
    }
}
