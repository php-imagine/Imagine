<?php

namespace Imagine\Test\Gmagick;

use Imagine\Gmagick\Imagine;
use Imagine\Test\Constraint\AbstractIsImageEqualTest;

/**
 * @group gmagick
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

        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick is not installed');
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
