<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Gmagick;

use Imagine\Gmagick\Imagine;
use Imagine\Test\Effects\AbstractEffectsTest;

/**
 * @group ext-gmagick
 */
class EffectsTest extends AbstractEffectsTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick is not installed');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Effects\AbstractEffectsTest::testColorize()
     *
     * @expectedException \Imagine\Exception\RuntimeException
     */
    public function testColorize()
    {
        parent::testColorize();
    }

    protected function getImagine()
    {
        return new Imagine();
    }

    public function testConvolution()
    {
        $this->setExpectedException('RuntimeException');
        parent::testConvolution();
    }
}
