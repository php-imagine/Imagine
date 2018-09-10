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
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

/**
 * Drawer implementation using the Imagick PHP extension.
 */
final class Drawer implements DrawerInterface
{
    /**
     * @var \Imagick
     */
    private $imagick;

    /**
     * @param \Imagick $imagick
     */
    public function __construct(\Imagick $imagick)
    {
        $this->imagick = $imagick;
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
        $x = $center->getX();
        $y = $center->getY();
        $width = $size->getWidth();
        $height = $size->getHeight();

        try {
            $pixel = $this->getColor($color);
            $arc = new \ImagickDraw();

            $arc->setStrokeColor($pixel);
            $arc->setStrokeWidth($thickness);
            $arc->setFillColor('transparent');
            $arc->arc($x - $width / 2, $y - $height / 2, $x + $width / 2, $y + $height / 2, $start, $end);

            $this->imagick->drawImage($arc);

            $pixel->clear();
            $pixel->destroy();

            $arc->clear();
            $arc->destroy();
        } catch (\ImagickException $e) {
            throw new RuntimeException('Draw arc operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
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
        $x = $center->getX();
        $y = $center->getY();
        $width = $size->getWidth();
        $height = $size->getHeight();

        try {
            $pixel = $this->getColor($color);
            $chord = new \ImagickDraw();

            $chord->setStrokeColor($pixel);
            $chord->setStrokeWidth($thickness);

            if ($fill) {
                $chord->setFillColor($pixel);
            } else {
                $from = new Point(round($x + $width / 2 * cos(deg2rad($start))), round($y + $height / 2 * sin(deg2rad($start))));
                $to = new Point(round($x + $width / 2 * cos(deg2rad($end))), round($y + $height / 2 * sin(deg2rad($end))));
                $this->line($from, $to, $color, $thickness);
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
            throw new RuntimeException('Draw chord operation failed', $e->getCode(), $e);
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
        $width = $size->getWidth();
        $height = $size->getHeight();
        try {
            $pixel = $this->getColor($color);
            $ellipse = new \ImagickDraw();

            $ellipse->setStrokeColor($pixel);
            $ellipse->setStrokeWidth($thickness);

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
            throw new RuntimeException('Draw ellipse operation failed', $e->getCode(), $e);
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
        try {
            $pixel = $this->getColor($color);
            $line = new \ImagickDraw();

            $line->setStrokeColor($pixel);
            $line->setStrokeWidth($thickness);
            $line->setFillColor($pixel);
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
            throw new RuntimeException('Draw line operation failed', $e->getCode(), $e);
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
        $width = $size->getWidth();
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
     *
     * @see \Imagine\Draw\DrawerInterface::dot()
     */
    public function dot(PointInterface $position, ColorInterface $color)
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
            throw new RuntimeException('Draw point operation failed', $e->getCode(), $e);
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
        $minX = min($leftTop->getX(), $rightBottom->getX());
        $maxX = max($leftTop->getX(), $rightBottom->getX());
        $minY = min($leftTop->getY(), $rightBottom->getY());
        $maxY = max($leftTop->getY(), $rightBottom->getY());

        try {
            $pixel = $this->getColor($color);
            $rectangle = new \ImagickDraw();
            $rectangle->setStrokeColor($pixel);
            $rectangle->setStrokeWidth($thickness);

            if ($fill) {
                $rectangle->setFillColor($pixel);
            } else {
                $rectangle->setFillColor('transparent');
            }

            $rectangle->rectangle($minX, $minY, $maxX, $maxY);
            $this->imagick->drawImage($rectangle);

            $pixel->clear();
            $pixel->destroy();

            $rectangle->clear();
            $rectangle->destroy();
        } catch (\ImagickException $e) {
            throw new RuntimeException('Draw rectangle operation failed', $e->getCode(), $e);
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
        if (count($coordinates) < 3) {
            throw new InvalidArgumentException(sprintf('Polygon must consist of at least 3 coordinates, %d given', count($coordinates)));
        }

        $thickness = max(0, (int) round($thickness));
        if ($thickness === 0 && !$fill) {
            return $this;
        }
        $points = array_map(function (PointInterface $p) {
            return array('x' => $p->getX(), 'y' => $p->getY());
        }, $coordinates);

        try {
            $pixel = $this->getColor($color);
            $polygon = new \ImagickDraw();

            $polygon->setStrokeColor($pixel);
            $polygon->setStrokeWidth($thickness);

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
            throw new RuntimeException('Draw polygon operation failed', $e->getCode(), $e);
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
        try {
            $pixel = $this->getColor($font->getColor());
            $text = new \ImagickDraw();

            $text->setFont($font->getFile());
            /*
             * @see http://www.php.net/manual/en/imagick.queryfontmetrics.php#101027
             *
             * ensure font resolution is the same as GD's hard-coded 96
             */
            if (version_compare(phpversion('imagick'), '3.0.2', '>=')) {
                $text->setResolution(96, 96);
                $text->setFontSize($font->getSize());
            } else {
                $text->setFontSize((int) ($font->getSize() * (96 / 72)));
            }
            $text->setFillColor($pixel);
            $text->setTextAntialias(true);

            if ($width !== null) {
                $string = $font->wrapText($string, $width, $angle);
            }

            $info = $this->imagick->queryFontMetrics($text, $string);
            $rad = deg2rad($angle);
            $cos = cos($rad);
            $sin = sin($rad);

            // round(0 * $cos - 0 * $sin)
            $x1 = 0;
            $x2 = round($info['characterWidth'] * $cos - $info['characterHeight'] * $sin);
            // round(0 * $sin + 0 * $cos)
            $y1 = 0;
            $y2 = round($info['characterWidth'] * $sin + $info['characterHeight'] * $cos);

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
            throw new RuntimeException('Draw text operation failed', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * Gets specifically formatted color string from ColorInterface instance.
     *
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     *
     * @return string
     */
    private function getColor(ColorInterface $color)
    {
        $pixel = new \ImagickPixel((string) $color);
        $pixel->setColorValue(\Imagick::COLOR_ALPHA, $color->getAlpha() / 100);

        return $pixel;
    }
}
