<?php

namespace Imagine\Test\Gmagick;

use Imagine\Gmagick\Imagine;
use Imagine\Test\Draw\AbstractAlphaBlendingAwareDrawerTest;

/**
 * @group gmagick
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
        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick is not installed');
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
