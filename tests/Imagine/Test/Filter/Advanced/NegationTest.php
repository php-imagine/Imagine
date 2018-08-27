<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Filter\Advanced;

use Imagine\Filter\Advanced\Negation;
use Imagine\Test\Filter\FilterTestCase;

class NegationTest extends FilterTestCase
{
    public function testApplyingNegation()
    {
        $effects = $this->getMockBuilder('Imagine\Effects\EffectsInterface')->getMock();
        $effects
            ->expects($this->once())
            ->method('negative')
            ->will($this->returnValue($effects))
        ;

        $image = $this->getImage();
        $image
            ->expects($this->once())
            ->method('effects')
            ->will($this->returnValue($effects))
        ;

        $filter = new Negation();

        $this->assertEquals($image, $filter->apply($image));
    }
}
