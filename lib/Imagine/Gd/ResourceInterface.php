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

use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\PointInterface;

interface ResourceInterface
{
    /**
     * @return boolean
     *
     * @see imagealphablending
     */
    function disableAlphaBlending();

    /**
     * @return boolean
     *
     * @see imagealphablending
     */
    function enableAlphaBlending();

    /**
     * @return boolean
     *
     * @see imageantialias
     */
    function disableAntiAlias();

    /**
     * @return boolean
     *
     * @see imageantialias
     */
    function enableAntiAlias();

    /**
     * @param Imagine\Image\Color $color
     *
     * @return integer
     *
     * @see imagecolorallocatealpha
     */
    function colorToIndex(Color $color);

    /**
     * @param Imagine\Image\PointInterface $at
     *
     * @return integer
     *
     * @see imagecolorat
     */
    function colorat(PointInterface $at);

    /**
     * @param integer $index
     *
     * @return Imagine\Image\Color
     *
     * @see imagecolorsforindex
     */
    function indexToColor($index);

    /**
     * Copies a part of the given resource of size $box from position $from
     * into existing resource at position $to
     *
     * @param Imagine\Gd\ResourceInterface $destination
     * @param Imagine\Image\PointInterface $from
     * @param Imagine\Image\BoxInterface   $box
     * @param Imagine\Image\PointInterface $to
     *
     * @return boolean
     *
     * @see imagecopymerge
     */
    function copy(ResourceInterface $resource, PointInterface $from, BoxInterface $box, PointInterface $to);

    /**
     * Copies a part of the given resource of size $box from position $from
     * into existing resource at position $to and stretches it to size $size
     *
     * @param Imagine\Gd\ResourceInterface $destination
     * @param Imagine\Image\PointInterface $from
     * @param Imagine\Image\BoxInterface   $box
     * @param Imagine\Image\PointInterface $to
     * @param Imagine\Image\BoxInterface   $size
     *
     * @return boolean
     *
     * @see imagecopyresampled
     */
    function copyResized(ResourceInterface $resource, PointInterface $from, BoxInterface $box, PointInterface $to, BoxInterface $size);

    /**
     * @see imagedestroy
     *
     * @return boolean
     */
    function destroy();

    /**
     * Performs a flood fill starting at the given coordinate
     *
     * @param Imagine\Image\PointInterface $start
     * @param Imagine\Image\Color          $color
     *
     * @see imagefill
     *
     * @return boolean
     */
    function fill(PointInterface $start, Color $color);

    /**
     * @param integer $x1
     * @param integer $y1
     * @param integer $x2
     * @param integer $y2
     * @param integer $color
     *
     * @return boolean
     *
     * @see imagefilledrectangle
     */
    function filledrectangle($x1, $y1, $x2, $y2, $color);

    /**
     * @param integer $filtertype
     * @param mixed   $arg1
     * @param mixed   $arg2
     * @param mixed   $arg3
     * @param mixed   $arg4
     *
     * @return boolean
     *
     * @see imagefilter
     */
    function filter($filtertype, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null);

    /**
     * @param integer             $angle
     * @param Imagine\Image\Color $bgColor
     *
     * @return boolean
     *
     * @see imagerotate
     */
    function rotate($angle, Color $bgColor);

    /**
     * @return boolean
     *
     * @see imagesavealpha
     */
    function disableSaveAlpha();

    /**
     * @return boolean
     *
     * @see imagesavealpha
     */
    function enableSaveAlpha();

    /**
     * @param integer $x
     * @param integer $y
     * @param integer $color
     *
     * @return boolean
     *
     * @see imagesetpixel
     */
    function setpixel($x, $y, $color);

    /**
     * @see imagesy
     * @see imagesx
     *
     * @return Imagine\Image\BoxInterface
     */
    function box();

    /**
     * Gets original GD resource
     *
     * @return resource
     */
    function unwrap();

    /**
     * @param string  $filename
     * @param integer $quality
     * @param integer $filters
     *
     * @return boolean
     *
     * @see imagepng
     */
    function png($filename = null, $quality = null, $filters = null);

    /**
     * @param string  $filename
     * @param integer $quality
     *
     * @return boolean
     *
     * @see imagejpeg
     */
    function jpeg($filename = null, $quality = null);

    /**
     * @param string $filename
     *
     * @return boolean
     *
     * @see imagegif
     */
    function gif($filename = null);

    /**
     * @param string  $filename
     * @param integer $foreground
     *
     * @return boolean
     *
     * @see imagewbmp
     */
    function wbmp($filename = null, $foreground = null);

    /**
     * @param string  $filename
     * @param integer $foreground
     *
     * @return boolean
     *
     * @see imagexbm
     */
    function xbm($filename = null, $foreground = null);
}
