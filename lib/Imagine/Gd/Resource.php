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

class Resource implements ResourceInterface
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::alphablending()
     */
    public function alphablending($blendmode)
    {
        return imagealphablending($this->resource, $blendmode);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::antialias()
     */
    public function antialias($enabled)
    {
        return imageantialias($this->resource, $enabled);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::colorallocatealpha()
     */
    public function colorallocatealpha($red, $green, $blue, $alpha)
    {
        return imagecolorallocatealpha($this->resource, $red, $green, $blue, $alpha);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::colorat()
     */
    public function colorat($x, $y)
    {
        return imagecolorat($this->resource, $x, $y);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::colorsforindex()
     */
    public function colorsforindex($index)
    {
        return imagecolorsforindex($this->resource, $index);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::copy()
     */
    public function copy(ResourceInterface $destination, $destinationX, $destinationY, $x, $y, $width, $height)
    {
        return imagecopy($destination->unwrap(), $this->resource, $destinationX, $destinationY, $x, $y, $width, $height);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::copymerge()
     */
    public function copymerge(ResourceInterface $destination, $destinationX, $destinationY, $x, $y, $width, $height, $pct = 100)
    {
        return imagecopymerge($destination->unwrap(), $this->resource, $destinationX, $destinationY, $x, $y, $width, $height, $pct);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::copyresampled()
     */
    public function copyresampled(ResourceInterface $destination, $destinationX, $destinationY, $destinationWidth, $destinationHeight, $x, $y, $width, $height)
    {
        return imagecopyresampled($destination->unwrap(), $this->resource, $destinationX, $destinationY, $x, $y, $destinationWidth, $destinationHeight, $width, $height);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::destroy()
     */
    public function destroy()
    {
        return imagedestroy($this->resource);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::filledrectangle()
     */
    public function filledrectangle($x1, $y1, $x2, $y2, $color)
    {
        return imagefilledrectangle($this->resource, $x1, $y1, $x2, $y2, $color);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::filter()
     */
    public function filter($filtertype, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null)
    {
        return imagefilter($this->resource, $filtertype, $arg1, $arg2, $arg3, $arg4);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::rotate()
     */
    public function rotate($angle, $bgColor, $ignoreTransparent = null)
    {
        $result = imagerotate($this->resource, $angle, $bgColor, $ignoreTransparent);

        return false === $result ? $result : $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::savealpha()
     */
    public function savealpha($saveflag)
    {
        return imagesavealpha($this->resource, $saveflag);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::setpixel()
     */
    public function setpixel($x, $y, $color)
    {
        return imagesetpixel($this->resource, $x, $y, $color);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::sx()
     */
    public function sx()
    {
        return imagesx($this->resource);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::sy()
     */
    public function sy()
    {
        return imagesy($this->resource);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::unwrap()
     */
    public function unwrap()
    {
        return $this->resource;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::gif()
     */
    public function gif($filename = null)
    {
        return imagegif($this->resource, $filename);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::jpeg()
     */
    public function jpeg($filename = null, $quality = null)
    {
        return imagejpeg($this->resource, $filename, $quality);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::png()
     */
    public function png($filename = null, $quality = null, $filters = null)
    {
        return imagepng($this->resource, $filename, $quality, $filters);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::wbmp()
     */
    public function wbmp($filename = null, $foreground = null)
    {
        return imagewbmp($this->resource, $filename, $foreground);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::xbm()
     */
    public function xbm($filename = null, $foreground = null)
    {
        return imagexbm($this->resource, $filename, $foreground);
    }
}
