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

use Imagine\Draw\DrawerInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\AbstractFont;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\PointInterface;

/**
 * Drawer implementation using the GD library
 */
final class Drawer implements DrawerInterface
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * @var array
     */
    private $info;

    /**
     * Constructs Drawer with a given gd image resource
     *
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->loadGdInfo();
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function arc(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $thickness = 1)
    {
        imagesetthickness($this->resource, max(1, (int) $thickness));
        if (false === imagearc(
            $this->resource, $center->getX(), $center->getY(),
            $size->getWidth(), $size->getHeight(), $start, $end,
            $this->getColor($color)
        )) {
            throw new RuntimeException('Draw arc operation failed');
        }

        return $this;
    }

    /**
     * This function doesn't work properly because of a bug in GD
     *
     * {@inheritdoc}
     */
    public function chord(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $fill = false, $thickness = 1)
    {
        imagesetthickness($this->resource, max(1, (int) $thickness));
        if ($fill) {
            $style = IMG_ARC_CHORD;
        } else {
            $style = IMG_ARC_CHORD | IMG_ARC_NOFILL;
        }

        if (false === imagefilledarc(
            $this->resource, $center->getX(), $center->getY(),
            $size->getWidth(), $size->getHeight(), $start, $end,
            $this->getColor($color), $style
        )) {
            throw new RuntimeException('Draw chord operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function ellipse(PointInterface $center, BoxInterface $size, Color $color, $fill = false, $thickness = 1)
    {
        imagesetthickness($this->resource, max(1, (int) $thickness));
        if ($fill) {
            $callback = 'imagefilledellipse';
        } else {
            $callback = 'imageellipse';
        }

        if (false === $callback(
            $this->resource, $center->getX(), $center->getY(),
            $size->getWidth(), $size->getHeight(), $this->getColor($color))
        ) {
            throw new RuntimeException('Draw ellipse operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function line(PointInterface $start, PointInterface $end, Color $color, $thickness = 1)
    {
        imagesetthickness($this->resource, max(1, (int) $thickness));
        if (false === imageline(
            $this->resource, $start->getX(), $start->getY(),
            $end->getX(), $end->getY(), $this->getColor($color)
        )) {
            throw new RuntimeException('Draw line operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function pieSlice(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $fill = false, $thickness = 1)
    {
        imagesetthickness($this->resource, max(1, (int) $thickness));
        if ($fill) {
            $style = IMG_ARC_EDGED;
        } else {
            $style = IMG_ARC_EDGED | IMG_ARC_NOFILL;
        }

        if (false === imagefilledarc(
            $this->resource, $center->getX(), $center->getY(),
            $size->getWidth(), $size->getHeight(), $start, $end,
            $this->getColor($color), $style
        )) {
            throw new RuntimeException('Draw chord operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function dot(PointInterface $position, Color $color)
    {
        if (false === imagesetpixel(
            $this->resource, $position->getX(), $position->getY(),
            $this->getColor($color)
        )) {
            throw new RuntimeException('Draw point operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function polygon(array $coordinates, Color $color, $fill = false, $thickness = 1)
    {
        imagesetthickness($this->resource, max(1, (int) $thickness));
        if (count($coordinates) < 3) {
            throw new InvalidArgumentException(sprintf(
                'A polygon must consist of at least 3 points, %d given',
                count($coordinates)
            ));
        }

        $points = array();

        foreach ($coordinates as $coordinate) {
            if (!$coordinate instanceof PointInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Each entry in coordinates array must be instance of '.
                    'Imagine\Image\PointInterface, %s given', var_export($coordinate)
                ));
            }

            $points[] = $coordinate->getX();
            $points[] = $coordinate->getY();
        }

        if ($fill) {
            $callback = 'imagefilledpolygon';
        } else {
            $callback = 'imagepolygon';
        }

        if (false === $callback(
            $this->resource, $points, count($coordinates),
            $this->getColor($color)
        )) {
            throw new RuntimeException('Draw polygon operation failed');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function text($string, AbstractFont $font, PointInterface $position, $angle = 0)
    {
        if (!$this->info['FreeType Support']) {
            throw new RuntimeException('GD is not compiled with FreeType support');
        }

        $angle    = -1 * $angle;
        $fontsize = $font->getSize();
        $fontfile = $font->getFile();
        $x        = $position->getX();
        $y        = $position->getY() + $fontsize;

        if (false === imagealphablending($this->resource, true)) {
            throw new RuntimeException('Font mask operation failed');
        }

        if (false === imagefttext(
                $this->resource, $fontsize, $angle, $x, $y,
                $this->getColor($font->getColor()), $fontfile, $string
            )) {
            throw new RuntimeException('Font mask operation failed');
        }

        if (false === imagealphablending($this->resource, false)) {
            throw new RuntimeException('Font mask operation failed');
        }

        return $this;
    }

    /**
     * Internal
     *
     * Generates a GD color from Color instance
     *
     * @param Imagine\Image\Color $color
     *
     * @return resource
     *
     * @throws Imagine\Exception\RuntimeException
     */
    private function getColor(Color $color)
    {
        $gdColor = imagecolorallocatealpha(
            $this->resource,
            $color->getRed(), $color->getGreen(), $color->getBlue(),
            round(127 * $color->getAlpha() / 100)
        );
        if (false === $gdColor) {
            throw new RuntimeException(sprintf(
                'Unable to allocate color "RGB(%s, %s, %s)" with '.
                'transparency of %d percent', $color->getRed(),
                $color->getGreen(), $color->getBlue(), $color->getAlpha()
            ));
        }

        return $gdColor;
    }

    private function loadGdInfo()
    {
        if (!function_exists('gd_info')) {
            throw new RuntimeException('Gd not installed');
        }

        $this->info = gd_info();
    }
}
