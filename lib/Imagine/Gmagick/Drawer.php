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

use Imagine\Draw\DrawerInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\AbstractFont;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

/**
 * Drawer implementation using the Gmagick PHP extension
 */
final class Drawer implements DrawerInterface
{
    /**
     * @var \Gmagick
     */
    private $gmagick;

    /**
     * @param \Gmagick $gmagick
     */
    public function __construct(\Gmagick $gmagick)
    {
        $this->gmagick = $gmagick;
    }

    /**
     * {@inheritdoc}
     */
    public function arc(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $thickness = 1)
    {
        $x      = $center->getX();
        $y      = $center->getY();
        $width  = $size->getWidth();
        $height = $size->getHeight();

        try {
            $pixel = $this->getColor($color);
            $arc   = new \GmagickDraw();

            $arc->setstrokecolor($pixel);
            $arc->setstrokewidth(max(1, (int) $thickness));
            $arc->setfillcolor('transparent');
            $arc->arc(
                $x - $width / 2,
                $y - $height / 2,
                $x + $width / 2,
                $y + $height / 2,
                $start,
                $end
            );

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
     * {@inheritdoc}
     */
    public function chord(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $fill = false, $thickness = 1)
    {
        $x      = $center->getX();
        $y      = $center->getY();
        $width  = $size->getWidth();
        $height = $size->getHeight();

        try {
            $pixel = $this->getColor($color);
            $chord = new \GmagickDraw();

            $chord->setstrokecolor($pixel);
            $chord->setstrokewidth(max(1, (int) $thickness));

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
     * {@inheritdoc}
     */
    public function ellipse(PointInterface $center, BoxInterface $size, Color $color, $fill = false, $thickness = 1)
    {
        $width  = $size->getWidth();
        $height = $size->getHeight();

        try {
            $pixel   = $this->getColor($color);
            $ellipse = new \GmagickDraw();

            $ellipse->setstrokecolor($pixel);
            $ellipse->setstrokewidth(max(1, (int) $thickness));

            if ($fill) {
                $ellipse->setfillcolor($pixel);
            } else {
                $ellipse->setfillcolor('transparent');
            }

            $ellipse->ellipse(
                $center->getX(),
                $center->getY(),
                $width / 2,
                $height / 2,
                0, 360
            );

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
     * {@inheritdoc}
     */
    public function line(PointInterface $start, PointInterface $end, Color $color, $thickness = 1)
    {
        try {
            $pixel = $this->getColor($color);
            $line  = new \GmagickDraw();

            $line->setstrokecolor($pixel);
            $line->setstrokewidth(max(1, (int) $thickness));
            $line->setfillcolor($pixel);
            $line->line(
                $start->getX(),
                $start->getY(),
                $end->getX(),
                $end->getY()
            );

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
     * {@inheritdoc}
     */
    public function pieSlice(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $fill = false, $thickness = 1)
    {
        $width  = $size->getWidth();
        $height = $size->getHeight();

        $x1 = round($center->getX() + $width / 2 * cos(deg2rad($start)));
        $y1 = round($center->getY() + $height / 2 * sin(deg2rad($start)));
        $x2 = round($center->getX() + $width / 2 * cos(deg2rad($end)));
        $y2 = round($center->getY() + $height / 2 * sin(deg2rad($end)));

        if ($fill) {
            $this->chord($center, $size, $start, $end, $color, true, $thickness);
            $this->polygon(
                array(
                    $center,
                    new Point($x1, $y1),
                    new Point($x2, $y2),
                ),
                $color,
                true,
                $thickness
            );
        } else {
            $this->arc($center, $size, $start, $end, $color, $thickness);
            $this->line($center, new Point($x1, $y1), $color, $thickness);
            $this->line($center, new Point($x2, $y2), $color, $thickness);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function dot(PointInterface $position, Color $color)
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
     * {@inheritdoc}
     */
    public function polygon(array $coordinates, Color $color, $fill = false, $thickness = 1)
    {
        if (count($coordinates) < 3) {
            throw new InvalidArgumentException(sprintf(
                'Polygon must consist of at least 3 coordinates, %d given',
                count($coordinates)
            ));
        }

        $points = array_map(function(PointInterface $p) {
            return array('x' => $p->getX(), 'y' => $p->getY());
        }, $coordinates);

        try {
            $pixel   = $this->getColor($color);
            $polygon = new \GmagickDraw();

            $polygon->setstrokecolor($pixel);
            $polygon->setstrokewidth(max(1, (int) $thickness));

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
     * {@inheritdoc}
     */
    public function text($string, AbstractFont $font, PointInterface $position, $angle = 0)
    {
        try {
            $pixel = $this->getColor($font->getColor());
            $text  = new \GmagickDraw();

            $text->setfont($font->getFile());
            /**
             * @see http://www.php.net/manual/en/imagick.queryfontmetrics.php#101027
             *
             * ensure font resolution is the same as GD's hard-coded 96
             */
            $text->setfontsize((int) ($font->getSize() * (96 / 72)));
            $text->setfillcolor($pixel);

            $info = $this->gmagick->queryfontmetrics($text, $string);
            $rad  = deg2rad($angle);
            $cos  = cos($rad);
            $sin  = sin($rad);

            $x1 = round(0 * $cos - 0 * $sin);
            $x2 = round($info['textWidth'] * $cos - $info['textHeight'] * $sin);
            $y1 = round(0 * $sin + 0 * $cos);
            $y2 = round($info['textWidth'] * $sin + $info['textHeight'] * $cos);

            $xdiff = 0 - min($x1, $x2);
            $ydiff = 0 - min($y1, $y2);

            $this->gmagick->annotateimage(
                $text, $position->getX() + $x1 + $xdiff,
                $position->getY() + $y2 + $ydiff, $angle, $string
            );

            $pixel = null;

            $text = null;
        } catch (\GmagickException $e) {
            throw new RuntimeException(
                'Draw text operation failed', $e->getCode(), $e
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
        if (!$color->isOpaque()) {
            throw new InvalidArgumentException('Gmagick doesn\'t support transparency');
        }

        $pixel = new \GmagickPixel((string) $color);

        $pixel->setColorValue(
            \Gmagick::COLOR_OPACITY,
            number_format(abs(round($color->getAlpha() / 100, 1)), 1)
        );

        return $pixel;
    }

}
