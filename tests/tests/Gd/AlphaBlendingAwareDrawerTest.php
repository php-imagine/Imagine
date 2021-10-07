<?php

namespace Imagine\Test\Gd;

use Imagine\Gd\Imagine;
use Imagine\Test\Draw\AbstractAlphaBlendingAwareDrawerTest;

/**
 * @group gd
 */
class AlphaBlendingAwareDrawerTest extends AbstractAlphaBlendingAwareDrawerTest
{
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
