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
use Imagine\Point;
use Imagine\Draw\DrawerInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;

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
    public function arc(Point $center, $width, $height, $start, $end, Color $color)
    {
        $x = $center->getX();
        $y = $center->getY();

        $arc = new \ImagickDraw();
        $arc->setStrokeColor($this->getColor($color));
        $arc->setStrokeWidth(1);
        $arc->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

        if (false === $this->imagick->drawImage($arc)) {
            throw new RuntimeException('Draw arc operation failed');
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::chord()
     */
    public function chord(Point $center, $width, $height, $start, $end, Color $color, $fill = false)
    {
        $x = $center->getX();
        $y = $center->getY();

        $chord = new \ImagickDraw();
        $chord->setStrokeColor($this->getColor($color));
        $chord->setStrokeWidth(1);

        $x1 = $width * cos($start);
        $y1 = $height * cos($start);
        $x2 = $width * cos($end);
        $y2 = $height * cos($end);

        $chord->line($x1, $y1, $x2, $y2);

        if ($fill) {
            $chord->setFillColor($this->getColor($color));
        }
        $chord->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

        if (false === $this->imagick->drawImage($chord)) {
            throw new RuntimeException('Draw chord operation failed');
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::ellipse()
     */
    public function ellipse(Point $center, $width, $height, Color $color, $fill = false)
    {
        $x = $center->getX();
        $y = $center->getY();

        $ellipse = new \ImagickDraw();
        $ellipse->setStrokeColor($this->getColor($color));
        $ellipse->setStrokeWidth(1);

        if ($fill) {
            $ellipse->setFillColor($this->getColor($color));
        }

        $ellipse->ellipse($x, $y, $width, $height, 0, 360);

        if (false === $this->imagick->drawImage($ellipse)) {
            throw new RuntimeException('Ellipse operation failed');
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::line()
     */
    public function line(Point $start, Point $end, Color $color)
    {
        $x1 = $start->getX();
        $y1 = $start->getY();
        $x2 = $end->getX();
        $y2 = $end->getY();

        $line = new \ImagickDraw();
        $line->setStrokeColor($this->getColor($color));
        $line->setStrokeWidth(1);
        $line->line($x1, $y1, $x2, $y2);

        if (false === $this->imagick->drawImage($line)) {
            throw new RuntimeException('Draw line operation failed');
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::pieSlice()
     */
    public function pieSlice(Point $center, $width, $height, $start, $end, Color $color, $fill = false)
    {
        $x = $center->getX();
        $y = $center->getY();

        $slice = new \ImagickDraw();
        $slice->setStrokeColor($this->getColor($color));
        $slice->setStrokeWidth(1);

        $x1 = $width * cos($start);
        $y1 = $height * cos($start);
        $x2 = $width * cos($end);
        $y2 = $height * cos($end);

        if ($fill) {
            $slice->setFillColor($this->getColor($color));
            $slice->polygon(array(
                array(new Point($x, $y)),
                array(new Point($x1, $y1)),
                array(new Point($x2, $y2)),
            ));
        } else {
            $slice->line(new Point($x, $y), new Point($x1, $y1));
            $slice->line(new Point($x, $y), new Point($x2, $y2));
        }

        $slice->arc(new Point($x - $width / 2, $y - $height / 2), $x + $width / 2, $y + $height / 2, $start, $end);

        if (false === $this->imagick->drawImage($slice)) {
            throw new RuntimeException('Draw pie slice operation failed');
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::dot()
     */
    public function dot(Point $position, Color $color)
    {
        $x = $position->getX();
        $y = $position->getY();

        $point = new \ImagickDraw();

        $point->setFillColor($this->getColor($color));
        $point->point($x, $y);

        if (false === $this->imagick->drawimage($point)) {
            throw new RuntimeException('Draw point operation failed');
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::polygon()
     */
    public function polygon(array $coordinates, Color $color, $fill = false)
    {
        if (count($coordinates) < 3) {
            throw new InvalidArgumentException(sprintf('Polygon must consist '.
                'of at least 3 coordinates, %d given', count($coordinates)));
        }

        $points = array_map(function(Point $p)
        {
            return array('x' => $p->getX(), 'y' => $p->getY());
        }, $coordinates);

        $polygon = new \ImagickDraw();

        $polygon->setStrokeColor($this->getColor($color));
        $polygon->setStrokeWidth(1);

        if ($fill) {
            $polygon->setFillColor($this->getColor($color));
        }

        $polygon->polygon($coordinates);

        if (false === $this->imagick->drawImage($polygon)) {
            throw new RuntimeException('Draw polygon operation failed');
        }

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
