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

use Imagine\Draw\DrawerInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\AbstractFont;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

final class Drawer implements DrawerInterface
{
    /**
     * @var Imagick
     */
    private $imagick;

    /**
     * @param Imagick $imagick
     */
    public function __construct(\Imagick $imagick)
    {
        $this->imagick = $imagick;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw\DrawerInterface::arc()
     */
    public function arc(PointInterface $center, BoxInterface $size, $start, $end, Color $color)
    {
        $x      = $center->getX();
        $y      = $center->getY();
        $width  = $size->getWidth();
        $height = $size->getHeight();

        try {
            $pixel = $this->getColor($color);
            $arc   = new \ImagickDraw();

            $arc->setStrokeColor($pixel);
            $arc->setStrokeWidth(1);
            $arc->setFillColor('transparent');
            $arc->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

            $this->imagick->drawImage($arc);

            $pixel->clear();
            $pixel->destroy();

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
     * @see Imagine\Draw\DrawerInterface::chord()
     */
    public function chord(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $fill = false)
    {
        $x      = $center->getX();
        $y      = $center->getY();
        $width  = $size->getWidth();
        $height = $size->getHeight();

        try {
            $pixel = $this->getColor($color);
            $chord = new \ImagickDraw();

            $chord->setStrokeColor($pixel);
            $chord->setStrokeWidth(1);

            if ($fill) {
                $chord->setFillColor($pixel);
            } else {
                $this->line(
                    new Point(
                        round($x + $width / 2 * cos(deg2rad($start))),
                        round($y + $height / 2 * sin(deg2rad($start)))
                    ),
                    new Point(
                        round($x + $width / 2 * cos(deg2rad($end))),
                        round($y + $height / 2 * sin(deg2rad($end)))
                    ),
                    $color
                );

                $chord->setFillColor('transparent');
            }

            $chord->arc(
                $x - $width / 2,
                $y - $height / 2,
                $x + $width / 2,
                $y + $height / 2,
                $start,
                $end
            );

            $this->imagick->drawImage($chord);

            $pixel->clear();
            $pixel->destroy();

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
     * @see Imagine\Draw\DrawerInterface::ellipse()
     */
    public function ellipse(PointInterface $center, BoxInterface $size, Color $color, $fill = false)
    {
        $width  = $size->getWidth();
        $height = $size->getHeight();

        try {
            $pixel   = $this->getColor($color);
            $ellipse = new \ImagickDraw();

            $ellipse->setStrokeColor($pixel);
            $ellipse->setStrokeWidth(1);

            if ($fill) {
                $ellipse->setFillColor($pixel);
            } else {
                $ellipse->setFillColor('transparent');
            }

            $ellipse->ellipse(
                $center->getX(),
                $center->getY(),
                $width / 2,
                $height / 2,
                0, 360
            );

            if (false === $this->imagick->drawImage($ellipse)) {
                throw new RuntimeException('Ellipse operation failed');
            }

            $pixel->clear();
            $pixel->destroy();

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
     * @see Imagine\Draw\DrawerInterface::line()
     */
    public function line(PointInterface $start, PointInterface $end, Color $color)
    {
        try {
            $pixel = $this->getColor($color);
            $line  = new \ImagickDraw();

            $line->setStrokeColor($pixel);
            $line->setStrokeWidth(1);
            $line->line(
                $start->getX(),
                $start->getY(),
                $end->getX(),
                $end->getY()
            );

            $this->imagick->drawImage($line);

            $pixel->clear();
            $pixel->destroy();

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
     * @see Imagine\Draw\DrawerInterface::pieSlice()
     */
    public function pieSlice(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $fill = false)
    {
        $width  = $size->getWidth();
        $height = $size->getHeight();

        $x1 = round($center->getX() + $width / 2 * cos(deg2rad($start)));
        $y1 = round($center->getY() + $height / 2 * sin(deg2rad($start)));
        $x2 = round($center->getX() + $width / 2 * cos(deg2rad($end)));
        $y2 = round($center->getY() + $height / 2 * sin(deg2rad($end)));

        if ($fill) {
            $this->chord($center, $size, $start, $end, $color, true);
            $this->polygon(
                array(
                    $center,
                    new Point($x1, $y1),
                    new Point($x2, $y2),
                ),
                $color,
                true
            );
        } else {
            $this->arc($center, $size, $start, $end, $color);
            $this->line($center, new Point($x1, $y1), $color);
            $this->line($center, new Point($x2, $y2), $color);
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Draw\DrawerInterface::dot()
     */
    public function dot(PointInterface $position, Color $color)
    {
        $x = $position->getX();
        $y = $position->getY();

        try {
            $pixel = $this->getColor($color);
            $point = new \ImagickDraw();

            $point->setFillColor($pixel);
            $point->point($x, $y);

            $this->imagick->drawimage($point);

            $pixel->clear();
            $pixel->destroy();

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
     * @see Imagine\Draw\DrawerInterface::polygon()
     */
    public function polygon(array $coordinates, Color $color, $fill = false)
    {
        if (count($coordinates) < 3) {
            throw new InvalidArgumentException(sprintf(
                'Polygon must consist of at least 3 coordinates, %d given',
                count($coordinates)
            ));
        }

        $points = array_map(
            function(PointInterface $p)
            {
                return array('x' => $p->getX(), 'y' => $p->getY());
            },
            $coordinates
        );

        try {
            $pixel   = $this->getColor($color);
            $polygon = new \ImagickDraw();

            $polygon->setStrokeColor($pixel);
            $polygon->setStrokeWidth(1);

            if ($fill) {
                $polygon->setFillColor($pixel);
            } else {
                $polygon->setFillColor('transparent');
            }

            $polygon->polygon($points);

            $this->imagick->drawImage($polygon);

            $pixel->clear();
            $pixel->destroy();

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
     * (non-PHPdoc)
     * @see Imagine\Draw\DrawerInterface::text()
     */
    public function text($string, AbstractFont $font, PointInterface $position, $angle = 0)
    {
        try {
            $pixel = $this->getColor($font->getColor());
            $text  = new \ImagickDraw();

            $text->setFont($font->getFile());
            $text->setFontSize($font->getSize());
            $text->setFillColor($pixel);
            $text->setTextAntialias(true);

            $info = $this->imagick->queryFontMetrics($text, $string);
            $rad  = deg2rad($angle);
            $cos  = cos($rad);
            $sin  = sin($rad);

            $x1 = round(0 * $cos - 0 * $sin);
            $x2 = round($info['textWidth'] * $cos - $info['textHeight'] * $sin);
            $y1 = round(0 * $sin + 0 * $cos);
            $y2 = round($info['textWidth'] * $sin + $info['textHeight'] * $cos);

            $xdiff = 0 - min($x1, $x2);
            $ydiff = 0 - min($y1, $y2);

            $this->imagick->annotateImage(
                $text, $position->getX() + $x1 + $xdiff,
                $position->getY() + $y2 + $ydiff, $angle, $string
            );

            $pixel->clear();
            $pixel->destroy();

            $text->clear();
            $text->destroy();
        } catch (\ImagickException $e) {
            throw new RuntimeException(
                'Draw text operation failed', $e->getCode(), $e
            );
        }
    }

    /**
     * Gets specifically formatted color string from Color instance
     *
     * @param Imagine\Image\Color $color
     *
     * @return string
     */
    private function getColor(Color $color)
    {
        $pixel = new \ImagickPixel((string) $color);

        $pixel->setColorValue(
            \Imagick::COLOR_OPACITY,
            number_format(abs(round($color->getAlpha() / 100, 1)), 1)
        );

        return $pixel;
    }
}
