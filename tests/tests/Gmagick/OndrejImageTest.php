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

/**
 * @group always-skipped
 */
class OndrejImageTest extends ImageTest
{
    /**
     * @dataProvider inOutResultProvider
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @backupGlobals disabled
     * @backupStaticAttributes disabled
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testSaveWithoutFileExtension()
     */
    public function testSaveWithoutFileExtension($file, $in, $out)
    {
        parent::testSaveWithoutFileExtension($file, $in, $out);
    }
}
