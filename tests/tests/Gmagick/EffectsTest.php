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
 * @group gmagick
 */
class EffectsTest extends AbstractEffectsTest
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\ImagineTestCaseBase::setUpBase()
     */
    protected function setUpBase()
    {
        parent::setUpBase();

        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick is not installed');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Effects\AbstractEffectsTest::testColorize()
     */
    public function testColorize()
    {
        $this->isGoingToThrowException('Imagine\Exception\RuntimeException');
        parent::testColorize();
    }

    protected function getImagine()
    {
        return new Imagine();
    }

    public function testConvolution()
    {
        $gm = new \Gmagick();
        if (!method_exists($gm, 'convolveimage')) {
            // convolveimage has been added in gmagick 2.0.1RC2
            $this->isGoingToThrowException('Imagine\Exception\NotSupportedException');
        }

        parent::testConvolution();
    }
}
