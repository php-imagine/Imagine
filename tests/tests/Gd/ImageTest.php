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

use Imagine\Gd\DriverInfo;
use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;
use Imagine\Test\Image\AbstractImageTest;

/**
 * @group gd
 */
class ImageTest extends AbstractImageTest
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\InfoProvider::getDriverInfo()
     */
    public static function getDriverInfo($required = true)
    {
        return DriverInfo::get($required);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::getImageResolution()
     */
    protected function getImageResolution(ImageInterface $image)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::getSamplingFactors()
     */
    protected function getSamplingFactors(ImageInterface $image)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::provideFilters()
     */
    public function provideFilters()
    {
        return array(
            array(ImageInterface::FILTER_UNDEFINED),
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::providePalettes()
     */
    public function providePalettes()
    {
        return array(
            array('Imagine\Image\Palette\RGB', array(255, 0, 0)),
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::provideFromAndToPalettes()
     */
    public function provideFromAndToPalettes()
    {
        return array(
            array(
                'Imagine\Image\Palette\RGB',
                'Imagine\Image\Palette\RGB',
                array(10, 10, 10),
            ),
        );
    }
}
