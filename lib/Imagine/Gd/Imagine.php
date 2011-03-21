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

use Imagine\Image\Color;
use Imagine\Image\BoxInterface;
use Imagine\ImageInterface;
use Imagine\ImagineInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;

final class Imagine implements ImagineInterface
{
    /**
     * @var array
     */
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

    /**
     * @throws Imagine\Exception\RuntimeException
     */
    public function __construct()
    {
        if (!function_exists('gd_info')) {
            throw new RuntimeException('Gd not installed');
        }
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImagineInterface::create()
     */
    public function create(BoxInterface $size, Color $color = null)
    {
        $width  = $size->getWidth();
        $height = $size->getHeight();

        $resource = imagecreatetruecolor($width, $height);

        if (false === $resource) {
            throw new RuntimeException('Create operation failed');
        }

        $color = $color ? $color : new Color('fff');

        if (false === imagealphablending($resource, false) ||
            false === imagesavealpha($resource, true)) {
            throw new RuntimeException(
                'Could not set alphablending, savealpha and antialias values'
            );
        }

        if (function_exists('imageantialias')) {
            imageantialias($resource, true);
        }

        $color = imagecolorallocatealpha(
            $resource, $color->getRed(), $color->getGreen(), $color->getBlue(),
            round(127 * $color->getAlpha() / 100)
        );

        if (false === $color) {
            throw new RuntimeException('Unable to allocate color');
        }

        if (false === imagefilledrectangle(
            $resource, 0, 0, $width, $height, $color
        )) {
            throw new RuntimeException('Could not set background color fill');
        }

        return new Image($resource, $this);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImagineInterface::open()
     */
    public function open($path)
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf(
                'File %s doesn\'t exist', $path
            ));
        }

        $info = getimagesize($path);

        if (false === $info) {
            throw new RuntimeException('Could not collect image metadata');
        }

        list($width, $height, $type) = $info;

        $format = $this->types[$type];

        if (!function_exists('imagecreatefrom'.$format)) {
            throw new InvalidArgumentException(
                'Invalid image format specified, only "gif", "jpeg", "png", '.
                '"wbmp", "xbm" images are supported'
            );
        }

        $resource = call_user_func('imagecreatefrom'.$format, $path);

        if (!is_resource($resource)) {
            throw new RuntimeException(sprintf(
                'File "%s" could not be opened', $path
            ));
        }

        if (false === imagealphablending($resource, false) ||
            false === imagesavealpha($resource, true)) {
            throw new RuntimeException(
                'Could not set alphablending, savealpha and antialias values'
            );
        }

        if (function_exists('imageantialias')) {
            imageantialias($resource, true);
        }

        return new Image($resource);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImagineInterface::load()
     */
    public function load($string)
    {
        $resource = imagecreatefromstring($string);

        if (!is_resource($resource)) {
            throw new InvalidArgumentException('An image could not be created from the given input');
        }

        if (false === imagealphablending($resource, false) ||
            false === imagesavealpha($resource, true)) {
            throw new RuntimeException(
                'Could not set alphablending, savealpha and antialias values'
            );
        }

        if (function_exists('imageantialias')) {
            imageantialias($resource, true);
        }

        return new Image($resource);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImagineInterface::font()
     */
    public function font($file, $size, Color $color)
    {
        return new Font($file, $size, $color);
    }
}
