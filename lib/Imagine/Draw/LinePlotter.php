<?php
namespace Imagine\Draw;

use Imagine\Image\Box;
use Imagine\Image\FontInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\Point;
use Imagine\Image\PointCollection;
use Imagine\Image\PointInterface;

class LinePlotter implements PlotterInterface
{
    const DRAW_RESOLUTION = 10;

    /**@var DrawerInterface */
    private $drawer;

    /** @var  LineStyle */
    protected $style;

    /**
     * @param DrawerInterface $drawer
     */
    public function __construct(DrawerInterface $drawer = null)
    {
        $this->drawer = $drawer;
    }

    /**
     * @param DrawerInterface $drawer
     */
    public function setDrawer(DrawerInterface $drawer)
    {
        $this->drawer = $drawer;
    }

    /**
     * @param PointInterface $point1
     * @param PointInterface $point2
     * @param LineStyle|null $style
     */
    public function plot(PointInterface $point1, PointInterface $point2, LineStyle $style = null)
    {
        if (null === $style) {
            $style = $this->getStyle() ? $this->getStyle() : new LineStyle(new RGB(new RGBPalette(), array(0, 0, 0), 100));
        }

        switch ($style->getStyle()) {
            case LineStyle::LINE_DASHED:
                $this->drawDashed($point1, $point2, $style);
                break;
            case LineStyle::LINE_DOTTED:
                $this->drawDotted($point1, $point2, $style);
                break;
            default:
                $this->drawSolid($point1, $point2, $style);
        }
    }

    /**
     * @param PointCollection $collection
     * @param LineStyle|null $style
     */
    public function plotCollection(PointCollection $collection, LineStyle $style = null)
    {
        if (null === $style) {
            $style = $this->getStyle() ? $this->getStyle() : new LineStyle(new RGB(new RGBPalette(), array(0, 0, 0), 100));
        }

        while (false === $collection->isFinished()) {
            list($point1, $point2) = $collection->getNextPair();
            switch ($style->getStyle()) {
                case LineStyle::LINE_DASHED:
                    $this->drawDashed($point1, $point2, $style);
                    break;
                case LineStyle::LINE_DOTTED:
                    $this->drawDotted($point1, $point2, $style);
                    break;
                default:
                    $this->drawSolid($point1, $point2, $style);
            }
        }

    }

    /**
     * @return LineStyle|null
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param LineStyle|null $style
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }

    /**
     * @param PointInterface $p1
     * @param PointInterface $p2
     * @param LineStyle $style
     */
    protected function drawDashed(PointInterface $p1, PointInterface $p2, LineStyle $style)
    {
        if ($p1->getX() > $p2->getX()) {
            $pTmp = $p1;
            $p1 = $p2;
            $p2 = $pTmp;
        }

        $x1 = $p1->getX();
        $x2 = $p2->getX();
        $y1 = $p1->getY();
        $y2 = $p2->getY();
        $previousPoint = null;

        if (0 != ($x2 - $x1)) {

            $a = ($y2 - $y1) / ($x2 - $x1);
            $b = $y1 - $x1 * ($y2 - $y1) / ($x2 - $x1);

            $firstDash = true;
            $lastX = $x1;
            $lastY = $a * $lastX + $b;
            $spacingSqrt = $style->getSpacing() * $style->getSpacing();
            $increase = ($x2 - $x1) / self::DRAW_RESOLUTION >= 1 ? 1 : ($x2 - $x1) / self::DRAW_RESOLUTION;

            for ($x = $x1; $x <= $x2; $x+=$increase) {
                $y = $a * $x + $b;
                $distance = ($x - $lastX) * ($x - $lastX) + ($y - $lastY) * ($y - $lastY);
                if ($firstDash || null === $previousPoint && $distance > $spacingSqrt) {
                    $previousPoint = new Point($x, $y);
                    $lastX = $x;
                    $lastY = $y;
                } elseif (null != $previousPoint && $distance > $spacingSqrt) {
                    $this->drawer->line($previousPoint, new Point($x, $y), $style->getColor(), $style->getThickness());
                    $previousPoint = null;
                    $lastX = $x;
                    $lastY = $y;
                }
                $firstDash = false;
            }
        } else {
            if ($y1 > $y2) {
                $yTmp = $y2;
                $y2 = $y1;
                $y1 = $yTmp;
            }
            for ($y = $y1; $y <= $y2; $y += $style->getSpacing()) {
                if (null === $previousPoint) {
                    $previousPoint = new Point($x1, $y);
                } else {
                    $this->drawer->line($previousPoint, new Point($x1, $y), $style->getColor(), $style->getThickness());
                    $previousPoint = null;
                }
            }
        }
    }

    /**
     * @param PointInterface $p1
     * @param PointInterface $p2
     * @param LineStyle $style
     */
    protected function drawDotted(PointInterface $p1, PointInterface $p2, LineStyle $style)
    {
        if ($p1->getX() > $p2->getX()) {
            $pTmp = $p1;
            $p1 = $p2;
            $p2 = $pTmp;
        }

        $x1 = $p1->getX();
        $x2 = $p2->getX();
        $y1 = $p1->getY();
        $y2 = $p2->getY();

        if (0 != ($x2 - $x1)) {
            $a = ($y2 - $y1) / ($x2 - $x1);
            $b = $y1 - $x1 * ($y2 - $y1) / ($x2 - $x1);

            $spacingSqrt = $style->getSpacing() * $style->getSpacing();
            $firstDot = true;
            $prevX = $x1;
            $prevY = $a * $prevX + $b;

            $increase = ($x2 - $x1) / self::DRAW_RESOLUTION >= 1 ? 1 : ($x2 - $x1) / self::DRAW_RESOLUTION;
            for ($x = $x1; $x <= $x2; $x+=$increase) {
                $y = $a * $x + $b;
                $distance = ($x - $prevX) * ($x - $prevX) + ($y - $prevY) * ($y - $prevY);
                if ($firstDot || $distance >= $spacingSqrt) {
                    $prevX = $x;
                    $prevY = $y;
                    $firstDot = false;
                    $this->drawer->ellipse(new Point($x, $y), new Box($style->getThickness(), $style->getThickness()), $style->getColor(), true, 1);
                }
            }
        } else {
            if ($y1 > $y2) {
                $yTmp = $y2;
                $y2 = $y1;
                $y1 = $yTmp;
            }
            for ($y = $y1; $y <= $y2; $y += $style->getSpacing()) {
                $this->drawer->ellipse(new Point($x1, $y), new Box($style->getThickness(), $style->getThickness()), $style->getColor(), 1);
            }
        }
    }

    /**
     * @param PointInterface $p1
     * @param PointInterface $p2
     * @param LineStyle $style
     */
    protected function drawSolid(PointInterface $p1, PointInterface $p2, LineStyle $style)
    {
        $this->drawer->line($p1, $p2, $style->getColor(), $style->getThickness());
    }

    /**
     * @param PointInterface $p
     * @param $text
     * @param FontInterface $font
     */
    public function label(PointInterface $p, $text, FontInterface $font)
    {
        $this->drawer->text($text, $font, $p);
    }
}
