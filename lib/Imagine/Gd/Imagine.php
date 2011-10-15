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
use Imagine\Image\ImagineInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;

final class Imagine implements ImagineInterface
{
    /**
     * @var array
     */
    private $types = array(
        IMAGETYPE_GIF      => 'gif',
        IMAGETYPE_JPEG     => 'jpeg',
        IMAGETYPE_JPEG2000 => 'jpeg',
        IMAGETYPE_PNG      => 'png',
        IMAGETYPE_UNKNOWN  => 'unknown',
        IMAGETYPE_WBMP     => 'wbmp',
        IMAGETYPE_XBM      => 'xbm'
    );

    /**
     * @var array
     */
    private $info;

    /**
     * @throws Imagine\Exception\RuntimeException
     */
    public function __construct()
    {
        $this->loadGdInfo();
        $this->requireGdVersion('2.0.1');
    }

    private function loadGdInfo()
    {
        if (!function_exists('gd_info')) {
            throw new RuntimeException('Gd not installed');
        }

        $this->info = gd_info();
    }

    private function requireGdVersion($version)
    {
        if (version_compare(GD_VERSION, $version, '<')) {
            throw new RuntimeException('GD2 version 2.0.1 or higher is required');
        }
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImagineInterface::create()
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
     * @see Imagine\Image\ImagineInterface::open()
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

        $supported = array(
            'gif'  => 'GIF Read Support',
            'jpeg' => 'JPEG Support',
            'png'  => 'PNG Support',
            'wbmp' => 'WBMP Support',
            'xbm'  => 'XBM Support'
        );

        if (!$this->info[$supported[$format]]) {
            throw new RuntimeException(sprintf(
                'Installed version of GD doesn\'t support "%s" image format',
                $format
            ));
        }

        if (!function_exists('imagecreatefrom'.$format)) {
            throw new InvalidArgumentException(
                'Invalid image format specified, only "gif", "jpeg", "png", '.
                '"wbmp", "xbm" images are supported'
            );
        }

        $resource = call_user_func('imagecreatefrom'.$format, $path);

        //Active in php development version 5.3, surelly true in 5.4
        //Inactivate for compatibility
        if( "gif" === $format and false){
            $index = imagecolortransparent($resource);
            if($index != (-1)){
                $color = ImageColorsForIndex($resource, $index);
                imagecolorset( $resource, $index, $color['red'], $color['green'], $color['blue'], 127 );
            }
        }

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
     * @see Imagine\Image\ImagineInterface::load()
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
     * @see Imagine\Image\ImagineInterface::font()
     */
    public function font($file, $size, Color $color)
    {
        if (!$this->info['FreeType Support']) {
            throw new RuntimeException('GD is not compiled with FreeType support');
        }

        return new Font($file, $size, $color);
    }
}
