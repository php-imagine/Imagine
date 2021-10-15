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

use Imagine\Draw\AlphaBlendingAwareDrawerInterface;
use Imagine\Driver\InfoProvider;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\AbstractFont;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB as RGBColor;
use Imagine\Image\PointInterface;

/**
 * Drawer implementation using the GD PHP extension.
 */
final class Drawer implements AlphaBlendingAwareDrawerInterface, InfoProvider
{
    /**
     * @var resource|\GdImage
     */
    private $resource;

    /**
     * @var bool
     */
    private $alphaBlending = true;

    /**
     * Constructs Drawer with a given gd image resource.
     *
     * @param resource|\GdImage $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

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
     * @see \Imagine\Draw\DrawerInterface::arc()
     */
    public function arc(PointInterface $center, BoxInterface $size, $start, $end, ColorInterface $color, $thickness = 1)
    {
        $thickness = max(0, (int) round($thickness));
        if ($thickness === 0) {
            return $this;
        }
        imagesetthickness($this->resource, $thickness);

        if ($this->applyAlphaBlending() === false) {
            throw new RuntimeException('Draw arc operation failed');
        }

        if (imagearc($this->resource, $center->getX(), $center->getY(), $size->getWidth(), $size->getHeight(), $start, $end, $this->getColor($color)) === false) {
            $this->revertAlphaBlending();
            throw new RuntimeException('Draw arc operation failed');
        }

        if ($this->revertAlphaBlending() === false) {
            throw new RuntimeException('Draw arc operation failed');
        }

        return $this;
    }

    /**
     * This function does not work properly because of a bug in GD.
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Draw\DrawerInterface::chord()
     */
    public function chord(PointInterface $center, BoxInterface $size, $start, $end, ColorInterface $color, $fill = false, $thickness = 1)
    {
        $thickness = max(0, (int) round($thickness));
        if ($thickness === 0 && !$fill) {
            return $this;
        }
        imagesetthickness($this->resource, $thickness);

        if ($this->applyAlphaBlending() === false) {
            throw new RuntimeException('Draw chord operation failed');
        }

        if ($fill) {
            $style = IMG_ARC_CHORD;
            if (imagefilledarc($this->resource, $center->getX(), $center->getY(), $size->getWidth(), $size->getHeight(), $start, $end, $this->getColor($color), $style) === false) {
                $this->revertAlphaBlending();
                throw new RuntimeException('Draw chord operation failed');
            }
        } else {
            foreach (array(IMG_ARC_NOFILL, IMG_ARC_NOFILL | IMG_ARC_CHORD) as $style) {
                if (imagefilledarc($this->resource, $center->getX(), $center->getY(), $size->getWidth(), $size->getHeight(), $start, $end, $this->getColor($color), $style) === false) {
                    $this->revertAlphaBlending();
                    throw new RuntimeException('Draw chord operation failed');
                }
            }
        }

        if ($this->revertAlphaBlending() === false) {
            throw new RuntimeException('Draw chord operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Draw\DrawerInterface::circle()
     */
    public function circle(PointInterface $center, $radius, ColorInterface $color, $fill = false, $thickness = 1)
    {
        $diameter = $radius * 2;

        return $this->ellipse($center, new Box($diameter, $diameter), $color, $fill, $thickness);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Draw\DrawerInterface::ellipse()
     */
    public function ellipse(PointInterface $center, BoxInterface $size, ColorInterface $color, $fill = false, $thickness = 1)
    {
        $thickness = max(0, (int) round($thickness));
        if ($thickness === 0 && !$fill) {
            return $this;
        }
        if (function_exists('imageantialias')) {
            imageantialias($this->resource, true);
        }
        imagesetthickness($this->resource, $thickness);

        if ($fill) {
            $callback = 'imagefilledellipse';
        } else {
            $callback = 'imageellipse';
        }

        if (function_exists('imageantialias')) {
            imageantialias($this->resource, true);
        }
        if ($this->applyAlphaBlending() === false) {
            throw new RuntimeException('Draw ellipse operation failed');
        }

        if (function_exists('imageantialias')) {
            imageantialias($this->resource, true);
        }
        if ($callback($this->resource, $center->getX(), $center->getY(), $size->getWidth(), $size->getHeight(), $this->getColor($color)) === false) {
            $this->revertAlphaBlending();
            throw new RuntimeException('Draw ellipse operation failed');
        }

        if ($this->revertAlphaBlending() === false) {
            throw new RuntimeException('Draw ellipse operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Draw\DrawerInterface::line()
     */
    public function line(PointInterface $start, PointInterface $end, ColorInterface $color, $thickness = 1)
    {
        $thickness = max(0, (int) round($thickness));
        if ($thickness === 0) {
            return $this;
        }
        imagesetthickness($this->resource, $thickness);

        if ($this->applyAlphaBlending() === false) {
            throw new RuntimeException('Draw line operation failed');
        }

        if (imageline($this->resource, $start->getX(), $start->getY(), $end->getX(), $end->getY(), $this->getColor($color)) === false) {
            $this->revertAlphaBlending();
            throw new RuntimeException('Draw line operation failed');
        }

        if ($this->revertAlphaBlending() === false) {
            throw new RuntimeException('Draw line operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Draw\DrawerInterface::pieSlice()
     */
    public function pieSlice(PointInterface $center, BoxInterface $size, $start, $end, ColorInterface $color, $fill = false, $thickness = 1)
    {
        $thickness = max(0, (int) round($thickness));
        if ($thickness === 0 && !$fill) {
            return $this;
        }
        imagesetthickness($this->resource, $thickness);

        if ($fill) {
            $style = IMG_ARC_EDGED;
        } else {
            $style = IMG_ARC_EDGED | IMG_ARC_NOFILL;
        }

        if ($this->applyAlphaBlending() === false) {
            throw new RuntimeException('Draw chord operation failed');
        }

        if (imagefilledarc($this->resource, $center->getX(), $center->getY(), $size->getWidth(), $size->getHeight(), $start, $end, $this->getColor($color), $style) === false) {
            $this->revertAlphaBlending();
            throw new RuntimeException('Draw chord operation failed');
        }

        if ($this->revertAlphaBlending() === false) {
            throw new RuntimeException('Draw chord operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Draw\DrawerInterface::dot()
     */
    public function dot(PointInterface $position, ColorInterface $color)
    {
        if ($this->applyAlphaBlending() === false) {
            throw new RuntimeException('Draw point operation failed');
        }

        if (imagesetpixel($this->resource, $position->getX(), $position->getY(), $this->getColor($color)) === false) {
            $this->revertAlphaBlending();
            throw new RuntimeException('Draw point operation failed');
        }

        if ($this->revertAlphaBlending() === false) {
            throw new RuntimeException('Draw point operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Draw\DrawerInterface::rectangle()
     */
    public function rectangle(PointInterface $leftTop, PointInterface $rightBottom, ColorInterface $color, $fill = false, $thickness = 1)
    {
        $thickness = max(0, (int) round($thickness));
        if ($thickness === 0 && !$fill) {
            return $this;
        }
        imagesetthickness($this->resource, $thickness);

        $minX = min($leftTop->getX(), $rightBottom->getX());
        $maxX = max($leftTop->getX(), $rightBottom->getX());
        $minY = min($leftTop->getY(), $rightBottom->getY());
        $maxY = max($leftTop->getY(), $rightBottom->getY());

        if ($fill) {
            $callback = 'imagefilledrectangle';
        } else {
            $callback = 'imagerectangle';
        }

        if ($this->applyAlphaBlending() === false) {
            throw new RuntimeException('Draw polygon operation failed');
        }

        if ($callback($this->resource, $minX, $minY, $maxX, $maxY, $this->getColor($color)) === false) {
            $this->revertAlphaBlending();
            throw new RuntimeException('Draw polygon operation failed');
        }

        if ($this->revertAlphaBlending() === false) {
            throw new RuntimeException('Draw polygon operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Draw\DrawerInterface::polygon()
     */
    public function polygon(array $coordinates, ColorInterface $color, $fill = false, $thickness = 1)
    {
        $thickness = max(0, (int) round($thickness));
        if ($thickness === 0 && !$fill) {
            return $this;
        }
        imagesetthickness($this->resource, $thickness);

        if (count($coordinates) < 3) {
            throw new InvalidArgumentException(sprintf('A polygon must consist of at least 3 points, %d given', count($coordinates)));
        }

        $points = call_user_func_array('array_merge', array_map(function (PointInterface $p) {
            return array($p->getX(), $p->getY());
        }, $coordinates));

        if ($fill) {
            $callback = 'imagefilledpolygon';
        } else {
            $callback = 'imagepolygon';
        }

        if ($this->applyAlphaBlending() === false) {
            throw new RuntimeException('Draw polygon operation failed');
        }

        if (false === (
            PHP_VERSION_ID < 80000
                ? $callback($this->resource, $points, count($coordinates), $this->getColor($color))
                : $callback($this->resource, $points, $this->getColor($color))
        )) {
            $this->revertAlphaBlending();
            throw new RuntimeException('Draw polygon operation failed');
        }

        if ($this->revertAlphaBlending() === false) {
            throw new RuntimeException('Draw polygon operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Draw\DrawerInterface::text()
     */
    public function text($string, AbstractFont $font, PointInterface $position, $angle = 0, $width = null)
    {
        static::getDriverInfo()->requireFeature(DriverInfo::FEATURE_TEXTFUNCTIONS);
        $angle = -1 * $angle;
        $fontsize = $font->getSize();
        $fontfile = $font->getFile();
        $x = $position->getX();
        $y = $position->getY() + $fontsize;

        if ($width !== null) {
            $string = $font->wrapText($string, $width, $angle);
        }

        if ($this->applyAlphaBlending() === false) {
            throw new RuntimeException('Font mask operation failed');
        }

        if ($fontfile && DIRECTORY_SEPARATOR === '\\') {
            // On Windows imagefttext() throws a "Could not find/open font" error if $fontfile is not an absolute path.
            $fontfileRealpath = realpath($fontfile);
            if ($fontfileRealpath !== false) {
                $fontfile = $fontfileRealpath;
            }
        }
        if (imagefttext($this->resource, $fontsize, $angle, $x, $y, $this->getColor($font->getColor()), $fontfile, $string) === false) {
            $this->revertAlphaBlending();
            throw new RuntimeException('Font mask operation failed');
        }

        if ($this->revertAlphaBlending() === false) {
            throw new RuntimeException('Font mask operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Draw\AlphaBlendingAwareDrawerInterface::getAlphaBlending()
     */
    public function getAlphaBlending()
    {
        return $this->alphaBlending;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Draw\AlphaBlendingAwareDrawerInterface::setAlphaBlending()
     */
    public function setAlphaBlending($value)
    {
        $this->alphaBlending = (bool) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Draw\AlphaBlendingAwareDrawerInterface::withAlphaBlending()
     */
    public function withAlphaBlending($value)
    {
        $result = clone $this;

        return $result->setAlphaBlending($value);
    }

    /**
     * Generates a GD color from Color instance.
     *
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     *
     * @throws \Imagine\Exception\RuntimeException
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return resource
     */
    private function getColor(ColorInterface $color)
    {
        if (!$color instanceof RGBColor) {
            throw new InvalidArgumentException('GD driver only supports RGB colors');
        }

        $gdColor = imagecolorallocatealpha($this->resource, $color->getRed(), $color->getGreen(), $color->getBlue(), round((100 - $color->getAlpha()) * 127 / 100));
        if ($gdColor === false) {
            throw new RuntimeException(sprintf('Unable to allocate color "RGB(%s, %s, %s)" with transparency of %d percent', $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha()));
        }

        return $gdColor;
    }

    /**
     * Apply the alpha blending value.
     *
     * @param resource|\GdImage|null $to the GD image. If null we'll apply the alpha blending to the current resource.
     *
     * @return bool
     */
    protected function applyAlphaBlending($to = null)
    {
        return $this->getAlphaBlending() ? imagealphablending($to ? $to : $this->resource, true) : true;
    }

    /**
     * Revert the alpha blending value to the initial state.
     *
     * @param resource|\GdImage|null $to the GD image. If null we'll apply the alpha blending to the current resource.
     *
     * @return bool
     */
    protected function revertAlphaBlending($to = null)
    {
        return $this->getAlphaBlending() ? imagealphablending($to ? $to : $this->resource, false) : true;
    }
}
