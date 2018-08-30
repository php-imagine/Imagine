<?php

namespace Imagine\Test\Imagick;

use Imagine\Imagick\Imagine;
use Imagine\Test\Constraint\AbstractIsImageEqualTest;

/**
 * @group ext-imagick
 */
class IsImageEqualTest extends AbstractIsImageEqualTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Constraint\AbstractIsImageEqualTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }
}
