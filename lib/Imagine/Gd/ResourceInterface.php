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

interface ResourceInterface
{
    /**
     * @param boolean $blendmode
     *
     * @return boolean
     *
     * @see imagealphablending
     */
    function alphablending($blendmode);

    /**
     * @param boolean $enabled
     *
     * @return boolean
     *
     * @see imageantialias
     */
    function antialias($enabled);

    /**
     * @param integer $red
     * @param integer $green
     * @param integer $blue
     * @param integer $alpha
     *
     * @return integer
     *
     * @see imagecolorallocatealpha
     */
    function colorallocatealpha($red, $green, $blue, $alpha);

    /**
     * @param integer $x
     * @param integer $y
     *
     * @return integer
     *
     * @see imagecolorat
     */
    function colorat($x, $y);

    /**
     * @param integer $index
     *
     * @return array
     *
     * @see imagecolorsforindex
     */
    function colorsforindex($index);

    /**
     * @param Imagine\Gd\ResourceInterface $destination
     * @param integer                      $destinationX
     * @param integer                      $destinationY
     * @param integer                      $x
     * @param integer                      $y
     * @param integer                      $width
     * @param integer                      $height
     *
     * @return boolean
     *
     * @see imagecopy
     */
    function copy(ResourceInterface $destination, $destinationX, $destinationY, $x, $y, $width, $height);

    /**
     * @param Imagine\Gd\ResourceInterface $destination
     * @param integer                      $destinationX
     * @param integer                      $destinationY
     * @param integer                      $x
     * @param integer                      $y
     * @param integer                      $width
     * @param integer                      $height
     * @param integer                      $pct
     *
     * @return boolean
     *
     * @see imagecopymerge
     */
    function copymerge(ResourceInterface $destination, $destinationX, $destinationY, $x, $y, $width, $height, $pct = 100);

    /**
     * @param Imagine\Gd\ResourceInterface $destination
     * @param integer                      $destinationX
     * @param integer                      $destinationY
     * @param integer                      $destinationWidth
     * @param integer                      $destinationHeight
     * @param integer                      $x
     * @param integer                      $y
     * @param integer                      $width
     * @param integer                      $height
     *
     * @return boolean
     *
     * @see imagecopyresampled
     */
    function copyresampled(ResourceInterface $destination, $destinationX, $destinationY, $destinationWidth, $destinationHeight, $x, $y, $width, $height);

    /**
     * @see imagedestroy
     *
     * @return boolean
     */
    function destroy();

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
     * @param integer $angle
     * @param integer $bgColor
     * @param boolean $ignoreTransparent
     *
     * @return mixed
     *
     * @see imagerotate
     */
    function rotate($angle, $bgColor, $ignoreTransparent = null);

    /**
     * @param boolean $saveflag
     *
     * @return boolean
     *
     * @see imagesavealpha
     */
    function savealpha($saveflag);

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
     * @see imagesx
     *
     * @return integer
     */
    function sx();

    /**
     * @see imagesy
     *
     * @return integer
     */
    function sy();

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
