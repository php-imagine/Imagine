<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

use Imagine\Exception\InvalidArgumentException;

use Imagine\ImageInterface;

final class FileImage extends Image
{
    private $types = array(
        IMAGETYPE_BMP      => 'bmp',
        IMAGETYPE_COUNT    => 'count',
        IMAGETYPE_GIF      => 'gif',
        IMAGETYPE_ICO      => 'ico',
        IMAGETYPE_IFF      => 'iff',
        IMAGETYPE_JB2      => 'jb2',
        IMAGETYPE_JP2      => 'jp2',
        IMAGETYPE_JPC      => 'jpc',
        IMAGETYPE_JPEG     => 'jpeg',
        IMAGETYPE_JPEG2000 => 'jpeg',
        IMAGETYPE_JPX      => 'jpx',
        IMAGETYPE_PNG      => 'png',
        IMAGETYPE_PSD      => 'psd',
        IMAGETYPE_SWC      => 'swc',
        IMAGETYPE_SWF      => 'swf',
        IMAGETYPE_TIFF_II  => 'tiff',
        IMAGETYPE_TIFF_MM  => 'tiff',
        IMAGETYPE_UNKNOWN  => 'unknown',
        IMAGETYPE_WBMP     => 'wbmp',
        IMAGETYPE_XBM      => 'xbm'
    );

    public function __construct($path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('File %s doesn\'t '.
                'exist', $path));
        }

        list($width, $height, $type) = getimagesize($path);

        $format = $this->types[$type];

        if (!$this->supported($format)) {
            throw new InvalidArgumentException(sprintf('Image format "%s" is '.
                'not supported, only "%s" images are supported',
                $format, implode('", "', $this->supported())));
        }

        $this->width    = $width;
        $this->height   = $height;
        $this->resource = call_user_func('imagecreatefrom'.$format, $path);
    }
}
