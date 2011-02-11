<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Imagick;

use Imagine\Color;
use Imagine\Draw\DrawerInterface;
use Imagine\Exception\InvalidArgumentException;

final class Drawer implements DrawerInterface
{
    private $imagick;

    public function __construct(\Imagick $imagick)
    {
        $this->imagick = $imagick;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::arc()
     */
    public function arc($x, $y, $width, $height, $start, $end, Color $outline)
    {
        $arc = new \ImagickDraw();
        $arc->setStrokeColor($this->getColor($outline));
        $arc->setStrokeWidth(1);
        $arc->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

        $this->imagick->drawImage($arc);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::chord()
     */
    public function chord($x, $y, $width, $height, $start, $end, Color $outline, $fill = false)
    {
        $chord = new \ImagickDraw();
        $chord->setStrokeColor($this->getColor($outline));
        $chord->setStrokeWidth(1);

        $x1 = $width * cos($start);
        $y1 = $height * cos($start);
        $x2 = $width * cos($end);
        $y2 = $height * cos($end);

        $chord->line($x1, $y1, $x2, $y2);

        if ($fill) {
            $chord->setFillColor($this->getColor($outline));
        }
        $chord->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

        $this->imagick->drawImage($chord);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::ellipse()
     */
    public function ellipse($x, $y, $width, $height, Color $outline, $fill = false)
    {
        $ellipse = new \ImagickDraw();
        $ellipse->setStrokeColor($this->getColor($outline));
        $ellipse->setStrokeWidth(1);

        if ($fill) {
            $ellipse->setFillColor($this->getColor($outline));
        }

        $ellipse->ellipse($x, $y, $width, $height, 0, 360);

        $this->imagick->drawImage($ellipse);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::line()
     */
    public function line($x1, $y1, $x2, $y2, Color $outline)
    {
        $line = new \ImagickDraw();
        $line->setStrokeColor($this->getColor($outline));
        $line->setStrokeWidth(1);
        $line->line($x1, $y1, $x2, $y2);

        $this->imagick->drawImage($line);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::pieSlice()
     */
    public function pieSlice($x, $y, $width, $height, $start, $end, Color $outline, $fill = false)
    {
        $slice = new \ImagickDraw();
        $slice->setStrokeColor($this->getColor($outline));
        $slice->setStrokeWidth(1);

        $x1 = $width * cos($start);
        $y1 = $height * cos($start);
        $x2 = $width * cos($end);
        $y2 = $height * cos($end);

        if ($fill) {
            $slice->setFillColor($this->getColor($outline));
            $slice->polygon(array(
                array($x, $y),
                array($x1, $y1),
                array($x2, $y2),
            ));
        } else {
            $slice->line($x, $y, $x1, $y1);
            $slice->line($x, $y, $x2, $y2);
        }

        $slice->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

        $this->imagick->drawImage($slice);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::point()
     */
    public function point($x, $y, Color $color)
    {
        $point = new \ImagickDraw();

        $point->setFillColor($this->getColor($color));
        $point->point($x, $y);

        $this->imagick->drawimage($point);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::polygon()
     */
    public function polygon(array $coordinates, Color $outline, $fill = false)
    {
        $polygon = new \ImagickDraw();

        $polygon->setStrokeColor($this->getColor($outline));
        $polygon->setStrokeWidth(1);

        if ($fill) {
            $polygon->setFillColor($this->getColor($outline));
        }

        $polygon->polygon($coordinates);

        $this->imagick->drawImage($polygon);

        return $this;
    }

    /**
     * Gets specifically formatted color string from Color instance
     *
     * @param Color $color
     *
     * @return string
     */
    private function getColor(Color $color)
    {
        return new \ImagickPixel(sprintf('rgba(%d,%d,%d,%d)',
            $color->getRed(), $color->getGreen(), $color->getBlue(),
            round($color->getAlpha() / 100, 1)));
    }
}
