<?php
namespace Imagine\Filter\Chart;

use Imagine\Draw\LineStyle;
use Imagine\Draw\PlotterInterface;
use Imagine\Filter\Chart\Data\DataPoint;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\Point;
use Imagine\Image\PointCollection;

class GridDrawer implements GridDrawerInterface
{
    /** @var int */
    protected $markerSize = 1;

    /** @var PlotterInterface */
    private $plotter;

    /** @var ChartConfig */
    private $config;

    /** @var  LineStyle */
    private $axesStyle;

    /** @var  LineStyle */
    private $gridStyle;

    /** @var float */
    private $scaleStepY;

    /** @var float */
    private $scaleStepX;

    /**
     * @param ChartConfig $config
     * @param PlotterInterface $plotter
     */
    public function __construct(ChartConfig $config = null, PlotterInterface $plotter = null)
    {
        $this->config = $config;
        $this->plotter = $plotter;

        //set defaults
        $this->markerSize = 1;
        $this->scaleStepX = 5.0;
        $this->scaleStepY = 5.0;
        $this->axesStyle = new LineStyle(new RGB(new RGBPalette(), array(0, 0, 0), 100), LineStyle::LINE_SOLID, 2);
        $this->gridStyle = new LineStyle(new RGB(new RGBPalette(), array(50, 50, 50), 60), LineStyle::LINE_DASHED, 1, 2);
    }

    /**
     * @param ChartConfig $config
     */
    public function setChartConfig(ChartConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param PlotterInterface $plotter
     */
    public function setPlotter(PlotterInterface $plotter)
    {
        $this->plotter = $plotter;
    }

    /**
     * @param LineStyle $axesStyle
     */
    public function setAxesStyle($axesStyle)
    {
        $this->axesStyle = $axesStyle;
    }

    /**
     * @param LineStyle $gridStyle
     */
    public function setGridStyle($gridStyle)
    {
        $this->gridStyle = $gridStyle;
    }

    /**
     * @param int $markerSize
     */
    public function setMarkerSize($markerSize)
    {
        $this->markerSize = $markerSize;
    }

    /**
     * @param float $scaleStep
     */
    public function setScaleStep($scaleStep)
    {
        $this->setScaleStepX($scaleStep);
        $this->setScaleStepY($scaleStep);
    }

    /**
     * @param float $scaleStepX
     */
    public function setScaleStepX($scaleStepX)
    {
        $this->scaleStepX = $scaleStepX;
    }

    /**
     * @param float $scaleStepY
     */
    public function setScaleStepY($scaleStepY)
    {
        $this->scaleStepY = $scaleStepY;
    }

    public function drawAxes()
    {
        $this->plotter->plot(
            new Point($this->config->getOriginX(), $this->config->getMarginY()),
            new Point($this->config->getOriginX(), $this->config->getImageHeight() - $this->config->getMarginY()),
            $this->axesStyle
        );

        $this->plotter->plot(
            new Point($this->config->getMarginX(), $this->config->getOriginY()),
            new Point($this->config->getImageWidth() - $this->config->getMarginX(), $this->config->getOriginY()),
            $this->axesStyle
        );
    }

    public function drawBorder()
    {
        if ($this->config->getMarginX() > 0) {
            $this->plotter->plotCollection(
                new PointCollection(array(
                    new Point($this->config->getMarginX(), $this->config->getMarginY()),
                    new Point($this->config->getImageWidth() - $this->config->getMarginX(), $this->config->getMarginY()),
                    new Point($this->config->getImageWidth() - $this->config->getMarginX(), $this->config->getImageHeight() - $this->config->getMarginY()),
                    new Point($this->config->getMarginX(), $this->config->getImageHeight() - $this->config->getMarginY()),
                    new Point($this->config->getMarginX(), $this->config->getMarginY())
                )),
                $this->axesStyle
            );
        }
    }

    public function drawMarkers()
    {
        for ($x = -$this->scaleStepX; $x >= $this->config->getNegativeXRange(); $x -= $this->scaleStepX) {
            $markerPoint = $this->config->scale(new DataPoint($x, 0));
            $start = $markerPoint->getY() - $this->markerSize > 0 ? $markerPoint->getY() - $this->markerSize : 0;
            $end = (($markerPoint->getY() + $this->markerSize) < $this->config->getImageHeight()) ? $markerPoint->getY() + $this->markerSize : $this->config->getImageHeight();
            $this->plotter->plot(new Point($markerPoint->getX(), $start), new Point($markerPoint->getX(), $end), $this->axesStyle);
        }

        for ($x = 0; $x <= $this->config->getPositiveXRange(); $x += $this->scaleStepX) {
            $markerPoint = $this->config->scale(new DataPoint($x, 0));
            $start = $markerPoint->getY() - $this->markerSize > 0 ? $markerPoint->getY() - $this->markerSize : 0;
            $end = (($markerPoint->getY() + $this->markerSize) < $this->config->getImageHeight()) ? $markerPoint->getY() + $this->markerSize : $this->config->getImageHeight();
            $this->plotter->plot(new Point($markerPoint->getX(), $start), new Point($markerPoint->getX(), $end), $this->axesStyle);
        }

        for ($y = $this->scaleStepY; $y <= $this->config->getPositiveYRange(); $y += $this->scaleStepY) {
            $markerPoint = $this->config->scale(new DataPoint(0, $y));
            $start = $markerPoint->getX() - $this->markerSize > 0 ? $markerPoint->getX() - $this->markerSize : 0;
            $end = (($markerPoint->getX() + $this->markerSize) < $this->config->getImageWidth()) ? $markerPoint->getX() + $this->markerSize : $this->config->getImageWidth();
            $this->plotter->plot(new Point($start, $markerPoint->getY()), new Point($end, $markerPoint->getY()), $this->axesStyle);
        }

        for ($y = 0; $y >= $this->config->getNegativeYRange(); $y -= $this->scaleStepY) {
            $markerPoint = $this->config->scale(new DataPoint(0, $y));
            $start = $markerPoint->getX() - $this->markerSize > 0 ? $markerPoint->getX() - $this->markerSize : 0;
            $end = (($markerPoint->getX() + $this->markerSize) < $this->config->getImageWidth()) ? $markerPoint->getX() + $this->markerSize : $this->config->getImageWidth();
            $this->plotter->plot(new Point($start, $markerPoint->getY()), new Point($end, $markerPoint->getY()), $this->axesStyle);
        }
    }

    public function drawGrid()
    {
        for ($x = -$this->scaleStepX; $x >= $this->config->getNegativeXRange(); $x -= $this->scaleStepX) {
            $markerPoint = $this->config->scale(new DataPoint($x, 0));
            $this->plotter->plot(
                new Point($markerPoint->getX(), $this->config->getMarginY()),
                new Point($markerPoint->getX(), $this->config->getImageHeight() - $this->config->getMarginY()),
                $this->gridStyle
            );
        }

        for ($x = 0; $x <= $this->config->getPositiveXRange(); $x += $this->scaleStepX) {
            $markerPoint = $this->config->scale(new DataPoint($x, 0));
            $this->plotter->plot(
                new Point($markerPoint->getX(), $this->config->getMarginY()),
                new Point($markerPoint->getX(), $this->config->getImageHeight() - $this->config->getMarginY()),
                $this->gridStyle
            );
        }


        for ($y = $this->scaleStepY; $y <= $this->config->getPositiveYRange(); $y += $this->scaleStepY) {
            $markerPoint = $this->config->scale(new DataPoint(0, $y));
            $this->plotter->plot(
                new Point($this->config->getMarginX(), $markerPoint->getY()),
                new Point($this->config->getImageWidth() - $this->config->getMarginX(), $markerPoint->getY()),
                $this->gridStyle
            );
        }


        for ($y = 0; $y >= $this->config->getNegativeYRange(); $y -= $this->scaleStepY) {
            $markerPoint = $this->config->scale(new DataPoint(0, $y));
            $this->plotter->plot(
                new Point($this->config->getMarginX(), $markerPoint->getY()),
                new Point($this->config->getImageWidth() - $this->config->getMarginX(), $markerPoint->getY()),
                $this->gridStyle
            );
        }
    }

    public function drawLabels()
    {
        if (null === $this->config->getFont()) {
            return;
        }

        for ($x = -$this->scaleStepX; $x >= $this->config->getNegativeXRange(); $x -= $this->scaleStepX) {
            $markerPoint = $this->config->scale(new DataPoint($x, 0));
            $this->plotter->label(
                new Point($markerPoint->getX() - $this->config->getFont()->getSize(), $this->config->getImageHeight() - $this->config->getMarginY() + $this->config->getFont()->getSize()/2),
                (string)str_pad($x, 3, ' ', STR_PAD_LEFT),
                $this->config->getFont()
            );
        }

        for ($x = 0; $x <= $this->config->getPositiveXRange(); $x += $this->scaleStepX) {
            $markerPoint = $this->config->scale(new DataPoint($x, 0));
            $this->plotter->label(
                new Point($markerPoint->getX() - $this->config->getFont()->getSize(), $this->config->getImageHeight() - $this->config->getMarginY() + $this->config->getFont()->getSize()/2),
                (string)str_pad($x, 3, ' ', STR_PAD_LEFT),
                $this->config->getFont()
            );
        }

        for ($y = $this->scaleStepY; $y <= $this->config->getPositiveYRange(); $y += $this->scaleStepY) {
            $markerPoint = $this->config->scale(new DataPoint(0, $y));
            $this->plotter->label(
                new Point($this->config->getMarginX() - $this->config->getLabelMargin(), $markerPoint->getY() - $this->config->getFont()->getSize()),
                (string)str_pad($y, 3, ' ', STR_PAD_LEFT),
                $this->config->getFont()
            );
        }

        for ($y = 0; $y >= $this->config->getNegativeYRange(); $y -= $this->scaleStepY) {
            $markerPoint = $this->config->scale(new DataPoint(0, $y));
            $this->plotter->label(
                new Point($this->config->getMarginX() - $this->config->getLabelMargin(), $markerPoint->getY() - $this->config->getFont()->getSize()),
                (string)str_pad($y, 3, ' ', STR_PAD_LEFT),
                $this->config->getFont()
            );
        }
    }
}
