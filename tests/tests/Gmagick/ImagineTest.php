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
use Imagine\Test\Image\AbstractImagineTest;

/**
 * @group ext-gmagick
 */
class ImagineTest extends AbstractImagineTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick is not installed');
        }
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testCreateAlphaPrecision()
     */
    public function testCreateAlphaPrecision()
    {
        $this->markTestSkipped('Alpha transparency is not supported by Gmagick');
    }

    protected function getImagine()
    {
        return new Imagine();
    }
}
