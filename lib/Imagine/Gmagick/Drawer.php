<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gmagick;

use Imagine\Color;
use Imagine\Point;
use Imagine\Draw\DrawerInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;

final class Drawer implements DrawerInterface
{
    private $gmagick;

    public function __construct(\Gmagick $gmagick)
    {
        $this->gmagick = $gmagick;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::arc()
     */
    public function arc(Point $center, $width, $height, $start, $end, Color $color)
    {
        $x = $center->getX();
        $y = $center->getY();

        try {
            $pixel = $this->getColor($color);
            $arc   = new \GmagickDraw();

            $arc->setstrokecolor($pixel);
            $arc->setstrokewidth(1);
            $arc->setfillcolor('transparent');
            $arc->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

            $this->gmagick->drawImage($arc);

            $pixel = null;

            $arc = null;
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Draw arc operation failed', $e->getCode(), $e
            );
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

        try {
            $pixel = $this->getColor($color);
            $chord = new \GmagickDraw();

            $chord->setstrokecolor($pixel);
            $chord->setstrokewidth(1);

            if ($fill) {
                $chord->setfillcolor($pixel);
            } else {
                $x1 = round($x + $width / 2 * cos(deg2rad($start)));
                $y1 = round($y + $height / 2 * sin(deg2rad($start)));
                $x2 = round($x + $width / 2 * cos(deg2rad($end)));
                $y2 = round($y + $height / 2 * sin(deg2rad($end)));

                $this->line(new Point($x1, $y1), new Point($x2, $y2), $color);

                $chord->setfillcolor('transparent');
            }

            $chord->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

            $this->gmagick->drawImage($chord);

            $pixel = null;

            $chord = null;
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Draw chord operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::ellipse()
     */
    public function ellipse(Point $center, $width, $height, Color $color, $fill = false)
    {
        try {
            $pixel   = $this->getColor($color);
            $ellipse = new \GmagickDraw();

            $ellipse->setstrokecolor($pixel);
            $ellipse->setstrokewidth(1);

            if ($fill) {
                $ellipse->setfillcolor($pixel);
            } else {
                $ellipse->setfillcolor('transparent');
            }

            $ellipse->ellipse($center->getX(), $center->getY(), $width, $height, 0, 360);

            $this->gmagick->drawImage($ellipse);

            $pixel = null;

            $ellipse = null;
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Draw ellipse operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::line()
     */
    public function line(Point $start, Point $end, Color $color)
    {
        try {
            $pixel = $this->getColor($color);
            $line  = new \GmagickDraw();

            $line->setstrokecolor($pixel);
            $line->setstrokewidth(1);
            $line->line($start->getX(), $start->getY(), $end->getX(), $end->getY());

            $this->gmagick->drawImage($line);

            $pixel = null;

            $line = null;
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Draw line operation failed', $e->getCode(), $e
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw.DrawerInterface::pieSlice()
     */
    public function pieSlice(Point $center, $width, $height, $start, $end, Color $color, $fill = false)
    {
        $x1 = round($center->getX() + $width / 2 * cos(deg2rad($start)));
        $y1 = round($center->getY() + $height / 2 * sin(deg2rad($start)));
        $x2 = round($center->getX() + $width / 2 * cos(deg2rad($end)));
        $y2 = round($center->getY() + $height / 2 * sin(deg2rad($end)));

        if ($fill) {
            $this->chord($center, $width, $height, $start, $end, $color, true);
            $this->polygon(array(
                $center,
                new Point($x1, $y1),
                new Point($x2, $y2),
            ), $color, true);
        } else {
            $this->arc($center, $width, $height, $start, $end, $color);
            $this->line($center, new Point($x1, $y1), $color);
            $this->line($center, new Point($x2, $y2), $color);
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

        try {
            $pixel = $this->getColor($color);
            $point = new \GmagickDraw();

            $point->setfillcolor($pixel);
            $point->point($x, $y);

            $this->gmagick->drawimage($point);

            $pixel = null;

            $point = null;
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Draw point operation failed', $e->getCode(), $e
            );
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

        try {
            $pixel   = $this->getColor($color);
            $polygon = new \GmagickDraw();

            $polygon->setstrokecolor($pixel);
            $polygon->setstrokewidth(1);

            if ($fill) {
                $polygon->setfillcolor($pixel);
            } else {
                $polygon->setfillcolor('transparent');
            }

            $polygon->polygon($points);

            $this->gmagick->drawImage($polygon);

            $pixel = null;

            $polygon = null;
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Draw polygon operation failed', $e->getCode(), $e
            );
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
    protected function getColor(Color $color)
    {
        $pixel = new \GmagickPixel((string) $color);

        if ($color->getAlpha() > 0) {
            $opacity = number_format(abs(round($color->getAlpha() / 100, 1)), 1); 
            $pixel->setColorValue(\Gmagick::COLOR_OPACITY, $opacity);
        }

        return $pixel;
    }

}
