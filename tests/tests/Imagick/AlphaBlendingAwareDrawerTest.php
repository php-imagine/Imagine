<?php

namespace Imagine\Test\Imagick;

use Imagine\Imagick\Imagine;
use Imagine\Test\Draw\AbstractAlphaBlendingAwareDrawerTest;

/**
 * @group imagick
 * @group always-skipped
 */
class AlphaBlendingAwareDrawerTest extends AbstractAlphaBlendingAwareDrawerTest
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\ImagineTestCaseBase::setUpBase()
     */
    protected function setUpBase()
    {
        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
        }
        parent::setUpBase();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Draw\AbstractAlphaBlendingAwareDrawerTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }
}
