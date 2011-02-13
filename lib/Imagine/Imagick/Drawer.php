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

        try {
            $arc = new \ImagickDraw();
            $arc->setStrokeColor($this->getColor($color));
            $arc->setStrokeWidth(1);
            $arc->setFillColor('transparent');
            $arc->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

            $this->imagick->drawImage($arc);

            $arc->clear();
            $arc->destroy();
        } catch (\ImagickException $e) {
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
            $chord = new \ImagickDraw();
            $pixel = $this->getColor($color);

            $chord->setStrokeColor($pixel);
            $chord->setStrokeWidth(1);

            if ($fill) {
                $chord->setFillColor($pixel);
            } else {
                $x1 = round($x + $width / 2 * cos(deg2rad($start)));
                $y1 = round($y + $height / 2 * sin(deg2rad($start)));
                $x2 = round($x + $width / 2 * cos(deg2rad($end)));
                $y2 = round($y + $height / 2 * sin(deg2rad($end)));

                $this->line(new Point($x1, $y1), new Point($x2, $y2), $color);

                $chord->setFillColor('transparent');
            }

            $chord->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

            $this->imagick->drawImage($chord);

            $chord->clear();
            $chord->destroy();
        } catch (\ImagickException $e) {
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
            $ellipse = new \ImagickDraw();
            $pixel   = $this->getColor($color);

            $ellipse->setStrokeColor($pixel);
            $ellipse->setStrokeWidth(1);

            if ($fill) {
                $ellipse->setFillColor($pixel);
            } else {
                $ellipse->setFillColor('transparent');
            }

            $ellipse->ellipse($center->getX(), $center->getY(), $width, $height, 0, 360);

            if (false === $this->imagick->drawImage($ellipse)) {
                throw new RuntimeException('Ellipse operation failed');
            }

            $ellipse->clear();
            $ellipse->destroy();
        } catch (\ImagickException $e) {
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
            $line = new \ImagickDraw();
            $line->setStrokeColor($this->getColor($color));
            $line->setStrokeWidth(1);
            $line->line($start->getX(), $start->getY(), $end->getX(), $end->getY());

            $this->imagick->drawImage($line);

            $line->clear();
            $line->destroy();
        } catch (\ImagickException $e) {
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

        $this->ellipse(new Point(125, 100), 50, 50, new Color('fff'));

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
            $point = new \ImagickDraw();

            $point->setFillColor($this->getColor($color));
            $point->point($x, $y);

            $this->imagick->drawimage($point);

            $point->clear();
            $point->destroy();
        } catch (\ImagickException $e) {
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
            $polygon = new \ImagickDraw();
            $pixel   = $this->getColor($color);

            $polygon->setStrokeColor($pixel);
            $polygon->setStrokeWidth(1);

            if ($fill) {
                $polygon->setFillColor($pixel);
            } else {
                $polygon->setFillColor('transparent');
            }

            $polygon->polygon($points);

            $this->imagick->drawImage($polygon);

            $polygon->clear();
            $polygon->destroy();
        } catch (\ImagickException $e) {
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
    private function getColor(Color $color)
    {
        $pixel = new \ImagickPixel((string) $color);
        if ($color->getAlpha() > 0) {
            $opacity = number_format(abs(round($color->getAlpha() / 100, 1)), 1);
            $pixel->setColorValue(\Imagick::COLOR_OPACITY, $opacity);
        }

    	return $pixel;
    }
}
