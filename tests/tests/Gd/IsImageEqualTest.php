<?php

namespace Imagine\Test\Gd;

use Imagine\Gd\Imagine;
use Imagine\Test\Constraint\AbstractIsImageEqualTest;

/**
 * @group gd
 */
class IsImageEqualTest extends AbstractIsImageEqualTest
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\ImagineTestCaseBase::setUpBase()
     */
    protected function setUpBase()
    {
        parent::setUpBase();

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
