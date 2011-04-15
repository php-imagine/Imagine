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
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\PointInterface;

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
        if (!is_resource($resource)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Argument 1 of %s must be a resource, %s given',
                    __METHOD__,
                    gettype($resource)
                )
            );
        }

        $this->resource = $resource;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::colorToIndex()
     */
    public function colorToIndex(Color $color)
    {
        return imagecolorallocatealpha(
            $this->resource,
            $color->getRed(),
            $color->getGreen(),
            $color->getBlue(),
            round(127 * $color->getAlpha() / 100)
        );
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::colorat()
     */
    public function colorat(PointInterface $at)
    {
        return imagecolorat($this->resource, $at->getX(), $at->getY());
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::indexToColor()
     */
    public function indexToColor($index)
    {
        $data = imagecolorsforindex($this->resource, $index);

        return new Color(array(
            $data['red'],
            $data['green'],
            $data['blue']
        ), round($data['alpha'] / 127 * 100));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::copy()
     */
    public function copy(ResourceInterface $resource, PointInterface $from, PointInterface $to, BoxInterface $box);
    {
        return imagecopy($this->resource, $resource->unwrap(), $to->getX(), $to->getY(), $from->getX(), $from->getY(), $box->getWidth(), $box->getHeight());
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

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::disableAlphaBlending()
     */
    public function disableAlphaBlending()
    {
        return $this->alphablending(false);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::disableAntiAlias()
     */
    public function disableAntiAlias()
    {
        return $this->antialias(false);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::disableSaveAlpha()
     */
    public function disableSaveAlpha()
    {
        return $this->savealpha(false);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::enableAlphaBlending()
     */
    public function enableAlphaBlending()
    {
        return $this->alphablending(true);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::enableAntiAlias()
     */
    public function enableAntiAlias()
    {
        return $this->antialias(true);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\ResourceInterface::enableSaveAlpha()
     */
    public function enableSaveAlpha()
    {
        return $this->savealpha(true);
    }

    /**
     * @param boolean $blendmode
     *
     * @return boolean
     *
     * @see imagealphablending
     */
    private function alphablending($blendmode)
    {
        return imagealphablending($this->resource, $blendmode);
    }

    /**
     * @param boolean $enabled
     *
     * @return boolean
     *
     * @see imageantialias
     */
    private function antialias($enabled)
    {
        return imageantialias($this->resource, $enabled);
    }

    /**
     * @param boolean $saveflag
     *
     * @return boolean
     *
     * @see imagesavealpha
     */
    private function savealpha($saveflag)
    {
        return imagesavealpha($this->resource, $saveflag);
    }
}
