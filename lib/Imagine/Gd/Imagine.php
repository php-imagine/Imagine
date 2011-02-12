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

use Imagine\Color;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\ImagineInterface;

class Imagine implements ImagineInterface
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

    public function __construct()
    {
        if (!function_exists('gd_info')) {
            throw new RuntimeException('Gd not installed');
        }
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImagineInterface::create()
     */
    public function create($width, $height, Color $color = null)
    {
        $resource = imagecreatetruecolor($width, $height);

        if (false === $resource) {
            throw new RuntimeException('Create operation failed');
        }

        $color = $color ? $color : new Color('fff');

        if (false === imagealphablending($resource, false) ||
            false === imagesavealpha($resource, true)) {
            throw new RuntimeException('Could not set alphablending and '.
                'savealpha values');
        }

        $color = imagecolorallocatealpha($resource, $color->getRed(),
            $color->getGreen(), $color->getBlue(),
            round(127 * $color->getAlpha() / 100));

        if (false === $color) {
            throw new RuntimeException('Unable to allocate color');
        }

        if (false === imagefilledrectangle($resource, 0, 0, $width, $height,
            $color)) {
            throw new RuntimeException('Could not set background color fill');
        }

        return new Image($resource, $width, $height, $this);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImagineInterface::open()
     */
    public function open($path)
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf('File %s doesn\'t '.
                'exist', $path));
        }

        $info = getimagesize($path);

        if (false === $info) {
            throw new RuntimeException('Could not collect image metadata');
        }

        list($width, $height, $type) = $info;

        $format = $this->types[$type];

        if (!function_exists('imagecreatefrom'.$format)) {
            throw new InvalidArgumentException('Invalid image format '.
                'specified, only "gif", "jpeg", "png", "wbmp", "xbm" images '.
                'are supported');
        }

        $resource = call_user_func('imagecreatefrom'.$format, $path);

        if (false === $resource) {
            throw new RuntimeException(sprintf('File "%s" could not be opened',
                $path));
        }

        if (false === imagealphablending($resource, false) ||
            false === imagesavealpha($resource, true)) {
            throw new RuntimeException('Could not set alphablending and '.
                'savealpha values');
        }

        return new Image($resource, $width, $height, $this);
    }
}
