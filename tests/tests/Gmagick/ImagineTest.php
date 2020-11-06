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
 * @group gmagick
 */
class ImagineTest extends AbstractImagineTest
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

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testShouldOpenAWebPImage()
     */
    public function testShouldOpenAWebPImage()
    {
        $gmagick = new \Gmagick();
        if (!in_array('WEBP', $gmagick->queryformats('WEBP'), true)) {
            $this->markTestSkipped('Gmagick webp support is not enabled');
        }

        return parent::testShouldOpenAWebPImage();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }
}
