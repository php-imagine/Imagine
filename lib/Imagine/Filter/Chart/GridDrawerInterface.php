<?php
namespace Imagine\Filter\Chart;

use Imagine\Draw\PlotterInterface;

interface GridDrawerInterface
{
    /**
     * @param ChartConfig|null $config
     * @param PlotterInterface|null $plotter
     */
    public function __construct(ChartConfig $config = null, PlotterInterface $plotter = null);

    /**
     * @param ChartConfig $config
     */
    public function setChartConfig(ChartConfig $config);

    /**
     * @param PlotterInterface $plotter
     */
    public function setPlotter(PlotterInterface $plotter);

    public function drawAxes();

    public function drawMarkers();

    public function drawGrid();

    public function drawLabels();
}
