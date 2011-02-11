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

    public function arc($x, $y, $width, $height, $start, $end, Color $outline)
    {
        $arc = new \ImagickDraw();
        $arc->setStrokeColor($this->getColor($outline));
        $arc->setStrokeWidth(1);
        $arc->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

        $this->imagick->drawImage($arc);

        return $this;
    }

    public function chord($x, $y, $width, $height, $start, $end, Color $outline, $fill = false)
    {
        $chord = new \ImagickDraw();
        $chord->setStrokeColor($this->getColor($outline));
        $chord->setStrokeWidth(1);

        if ($fill) {
            $chord->setFillColor($this->getColor($outline));
        }

//        $chord->line($sx, $sy, $ex, $ey);
        $chord->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

        $this->imagick->drawImage($chord);

        return $this;
    }

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

    public function line($x1, $y1, $x2, $y2, Color $outline)
    {
        $line = new \ImagickDraw();
        $line->setStrokeColor($this->getColor($outline));
        $line->setStrokeWidth(1);
        $line->line($x1, $y1, $x2, $y2);

        $this->imagick->drawImage($line);

        return $this;
    }

    public function pieSlice($x, $y, $width, $height, $start, $end, Color $outline, $fill = false)
    {
        $slice = new \ImagickDraw();
        $slice->setStrokeColor($this->getColor($outline));
        $slice->setStrokeWidth(1);

        if ($fill) {
            $slice->setFillColor($this->getColor($outline));
        }

        $slice->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

        $this->imagick->drawImage($slice);

        return $this;
    }

    public function point($x, $y, Color $color)
    {
        // TODO Auto-generated method stub
    }

    public function polygon(array $coordinates, Color $outline, $fill = false)
    {
        // TODO Auto-generated method stub
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
