<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Gd;

use Imagine\Gd\Imagine;
use Imagine\Test\Draw\AbstractDrawerTest;

/**
 * @group gd
 */
class DrawerTest extends AbstractDrawerTest
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
     * @dataProvider thicknessAndFillProvider
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Draw\AbstractDrawerTest::testChord()
     */
    public function testChord($thickness, $fill)
    {
        if ($fill) {
            $this->markTestSkipped('The GD Drawer can NOT draw correctly filled chords');
        }
        parent::testChord($thickness, $fill);
    }

    /**
     * @dataProvider thicknessAndFillProvider
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Draw\AbstractDrawerTest::testCircle()
     */
    public function testCircle($thickness, $fill)
    {
        if (!$fill && $thickness > 1) {
            $this->markTestSkipped('The GD Drawer can NOT draw correctly not filled circles with a thickness greater than 1');
        }
        parent::testCircle($thickness, $fill);
    }

    /**
     * @dataProvider thicknessAndFillProvider
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Draw\AbstractDrawerTest::testEllipse()
     */
    public function testEllipse($thickness, $fill)
    {
        if (!$fill && $thickness > 1) {
            $this->markTestSkipped('The GD Drawer can NOT draw correctly not filled ellipses with a thickness greater than 1');
        }
        parent::testEllipse($thickness, $fill);
    }

    protected function getImagine()
    {
        return new Imagine();
    }

    protected function isFontTestSupported()
    {
        $infos = gd_info();

        return isset($infos['FreeType Support']) ? $infos['FreeType Support'] : false;
    }
}
