<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Mask\Gradient;

use Imagine\PointInterface;

abstract class LinearTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Imagine\Mask\MaskInterface
     */
    private $mask;

    protected function setUp()
    {
        $this->mask = $this->getMask();
    }

    /**
     * @dataProvider getPointsAndShades
     *
     * @param integer                $shade
     * @param Imagine\PointInterface $position
     */
    public function testShouldProvideCorrectShadeValues($shade, PointInterface $position)
    {
        $this->assertEquals($shade, $this->mask->getShade($position));
    }

    /**
     * @return Imagine\Mask\MaskInterface
     */
    abstract protected function getMask();

    /**
     * @return array
     */
    abstract public function getPointsAndShades();
}
