<?php

namespace Imagine\Test\Gd;

use Imagine\Gd\Imagine;
use Imagine\Test\Constraint\AbstractIsImageEqualTest;

/**
 * @group gd
 */
class IsImageEqualTest extends AbstractIsImageEqualTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
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
