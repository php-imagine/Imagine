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

use Imagine\Driver\InfoProvider;
use Imagine\Image\AbstractFont;

/**
 * Font implementation using the GD library.
 */
final class Font extends AbstractFont implements InfoProvider
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\InfoProvider::getDriverInfo()
     * @since 1.3.0
     */
    public static function getDriverInfo($required = true)
    {
        return DriverInfo::get($required);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\FontInterface::box()
     */
    public function box($string, $angle = 0)
    {
        static::getDriverInfo()->requireFeature(DriverInfo::FEATURE_TEXTFUNCTIONS);
        $fontfile = $this->file;
        if ($fontfile && DIRECTORY_SEPARATOR === '\\') {
            // On Windows imageftbbox() throws a "Could not find/open font" error if $fontfile is not an absolute path.
            $fontfileRealpath = realpath($fontfile);
            if ($fontfileRealpath !== false) {
                $fontfile = $fontfileRealpath;
            }
        }

        $angle = -1 * $angle;
        $info = imageftbbox($this->size, $angle, $fontfile, $string);
        $xs = array($info[0], $info[2], $info[4], $info[6]);
        $ys = array($info[1], $info[3], $info[5], $info[7]);
        $width = abs(max($xs) - min($xs));
        $height = abs(max($ys) - min($ys));

        return $this->getClassFactory()->createBox($width, $height);
    }
}
