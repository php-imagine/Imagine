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
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;

final class Drawer implements DrawerInterface
{
    private $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::arc()
     */
    public function arc($x, $y, $width, $height, $start, $end, Color $outline)
    {
        if ($width < 1 || $height < 1) {
            throw new OutOfBoundsException('Dimensions of the arc must be '.
                'positive numbers');
        }

        if (false === imagearc($this->resource, $x, $y, $width, $height,
            $start, $end, $this->getColor($outline))) {
            throw new RuntimeException('Draw arc operation failed');
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::chord()
     */
    public function chord($x, $y, $width, $height, $start, $end, Color $outline, $fill = false)
    {
        if ($width < 1 || $height < 1) {
            throw new OutOfBoundsException('Dimensions of the chord must be '.
                'positive numbers');
        }

        $style = $fill ? IMG_ARC_CHORD : IMG_ARC_CHORD | IMG_ARC_NOFILL;

        if (false === imagefilledarc($this->resource, $x, $y,
            $width, $height, $start, $end,
            $this->getColor($outline), $style)) {
            throw new RuntimeException('Draw chord operation failed');
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::ellipse()
     */
    public function ellipse($x, $y, $width, $height, Color $outline, $fill = false)
    {
        if ($width < 1 || $height < 1) {
            throw new OutOfBoundsException('Dimensions of the ellipse must '.
                'be positive numbers');
        }

        if ($fill) {
            if (false === imagefilledellipse($this->resource, $x, $y, $width,
                $height, $this->getColor($outline))) {
                throw new RuntimeException('Draw ellipse operation failed');
            }
        } else {
            if (false === imageellipse($this->resource, $x, $y, $width,
                $height, $this->getColor($outline))) {
                throw new RuntimeException('Draw ellipse operation failed');
            }
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::line()
     */
    public function line($x1, $y1, $x2, $y2, Color $outline)
    {
        if ($x1 < 0 || $y1 < 0 || $x2 < 0 || $y2 < 0) {
            throw new OutOfBoundsException('Coordinates of the start and the '.
                'end of the line must be positive numbers');
        }

        if (false === imageline($this->resource, $x1, $y1, $x2,
            $y2, $this->getColor($outline))) {
            throw new RuntimeException('Draw line operation failed');
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::pieSlice()
     */
    public function pieSlice($x, $y, $width, $height, $start, $end, Color $outline, $fill = false)
    {
        if ($width < 1 || $height < 1) {
            throw new OutOfBoundsException('Dimensions of the pie slice must '.
                'be positive numbers');
        }

        $style = (Boolean) $fill ? IMG_ARC_PIE : IMG_ARC_PIE | IMG_ARC_NOFILL;

        if (false === imagefilledarc($this->resource, $x, $y, $width, $height,
            $start, $end, $this->getColor($outline), $style)) {
            throw new RuntimeException('Draw pie slice operation failed');
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::point()
     */
    public function point($x, $y, Color $color)
    {
        if ($x < 0 || $y < 0) {
            throw new OutOfBoundsException('Coordinates or the target pixel '.
                'must start at minimum 0, 0 position from top left corner');
        }

        if (false === imagesetpixel($this->resource, $x, $y,
            $this->getColor($color))) {
            throw new RuntimeException('Draw point operation failed');
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::polygon()
     */
    public function polygon(array $coordinates, Color $outline, $fill = false)
    {
        if (count($coordinates) < 3) {
            throw new InvalidArgumentException(sprintf('A polygon must '.
                'consist of at least 3 points, %d given', count($coordinates)
            ));
        }

        if ($fill) {
            if (false === imagefilledpolygon($this->resource, $coordinates,
                count($coordinates), $this->getColor($outline))) {
                throw new RuntimeException('Draw polygon operation failed');
            }
        } else {
            if (false === imagepolygon($this->resource, $coordinates,
                count($coordinates), $this->getColor($outline))) {
                throw new RuntimeException('Draw polygon operation failed');
            }
        }

        return $this;
    }
    /**
     * Internal
     *
     * Generates a GD color from Color instance
     *
     * @param  Color    $color
     *
     * @throws RuntimeException
     *
     * @return resource
     */
    private function getColor(Color $color)
    {
        $color = imagecolorallocatealpha($this->resource, $color->getRed(),
            $color->getGreen(), $color->getBlue(),
            round(127 * $color->getAlpha() / 100));
        if (false === $color) {
            throw new RuntimeException(sprintf('Unable to allocate color '.
                '"RGB(%s, %s, %s)" with transparency of %d percent',
                $color->getRed(), $color->getGreen(), $color->getBlue(),
                $color->getAlpha()));
        }

        return $color;
    }
}
