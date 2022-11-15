<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Image;

use Imagine\Image\Format;
use Imagine\Test\ImagineTestCase;

class FormatTest extends ImagineTestCase
{
    public function formatIdMimeTypeProvider()
    {
        return array(
            array(Format::ID_JPEG, 'image/jpeg'),
            array(Format::ID_WBMP, 'image/vnd.wap.wbmp'),
            array(Format::ID_AVIF, 'image/avif'),
            array(Format::ID_WEBP, 'image/webp'),
        );
    }

    /**
     * @dataProvider formatIdMimeTypeProvider
     *
     * @param string $formatId
     * @param string $expectedMimeType
     */
    public function testMimeTypes($formatId, $expectedMimeType)
    {
        $format = Format::get($formatId);

        $this->assertEquals($format->getMimeType(), $expectedMimeType);
    }
}
