<?php
namespace Imagine\Filter\Chart;

use Imagine\Draw\LinePlotter;
use Imagine\Filter\Chart\Data\DataSet;
use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\Point;
use Imagine\Image\PointCollection;

class LineChart implements FilterInterface
{
    /** @var DataSet[] */
    protected $dataSets = array();

    /** @var ImagineInterface */
    private $imagine;

    /** @var LinePlotter */
    private $plotter;

    /** @var  ChartConfig */
    protected $config;

    /*** @var GridDrawerInterface*/
    private $gridDrawer;

    /** @var bool  */
    private $drawBorder;

    /** @var bool  */
    private $drawGrid;

    /** @var bool  */
    private $labelAxes;

    /** @var bool  */
    private $fitToData;

    /** @var Point */
    protected $origin;

    /** @var float  */
    private $padding = 1.0;

    /** @var float  */
    private $margin = 1.0;

    /** @var  string */
    protected $fontFace;

    /** @var  int */
    protected $fontSize;

    /**
     * @param ImagineInterface $imagine
     * @param LinePlotter $plotter
     * @param GridDrawerInterface $gridDrawer
     * @param bool $fitToData
     * @param bool $drawBorder
     * @param bool $drawGrid
     * @param bool $labelAxes
     */
    public function __construct(
        ImagineInterface $imagine,
        LinePlotter $plotter,
        GridDrawerInterface $gridDrawer,
        $fitToData = false,
        $drawBorder = true,
        $drawGrid = true,
        $labelAxes = true
    ) {
        $this->imagine = $imagine;
        $this->plotter = $plotter;
        $this->gridDrawer = $gridDrawer;

        $this->fitToData = $fitToData;
        $this->drawBorder = $drawBorder;
        $this->drawGrid = $drawGrid;
        $this->labelAxes = $labelAxes;
    }

    /**
     * @param array $data
     * @param null $lineStyle
     */
    public function addData(array $data, $lineStyle = null)
    {
        $this->addDataSet(new DataSet($data, $lineStyle));
    }

    /**
     * @param DataSet $dataSet
     */
    public function addDataSet(DataSet $dataSet)
    {
        $this->dataSets[] = $dataSet;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        try {
            $font =  $this->imagine->font($this->fontFace, $this->fontSize, new RGB(new RGBPalette(), array(0, 0, 0), 100));
        } catch (\Exception $e) {
            $font = null;
        }

        $this->config = new ChartConfig(
            $image,
            $this->dataSets,
            $this->fitToData,
            $this->margin,
            $this->padding,
            $this->labelAxes,
            $font
        );

        $this->origin = $this->config->getOrigin();
        $this->plotter->setDrawer($image->draw());
        $this->gridDrawer->setChartConfig($this->config);
        $this->gridDrawer->setPlotter($this->plotter);

        foreach ($this->dataSets as $dataSet) {
            $this->plotDataSet($dataSet);
        }

        $this->gridDrawer->drawAxes();
        $this->gridDrawer->drawMarkers();

        if (true === $this->drawBorder) { $this->gridDrawer->drawBorder();}
        if (true === $this->drawGrid) { $this->gridDrawer->drawGrid(); }
        if (true === $this->labelAxes) {$this->gridDrawer->drawLabels(); }

        return $image;
    }

    /**
     * @param DataSet $dataSet
     */
    protected function plotDataSet(DataSet $dataSet)
    {
        $points = $dataSet->getData();
        $scaledPoints = array();
        foreach ($points as $point) {
            if ($point = $this->config->scale($point)) {
                $scaledPoints[] = $point;
            }
        }
        $collection = new PointCollection($scaledPoints);
        $this->plotter->plotCollection($collection, $dataSet->getLineStyle());
    }

    /**
     * @param string $fontFace
     */
    public function setFontFace($fontFace)
    {
        $this->fontFace = $fontFace;
    }

    /**
     * @param string $fontSize
     */
    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;
    }

    /**
     * @param float $padding
     */
    public function setPaddingPercent($padding)
    {
        $this->padding = $padding;
    }

    /**
     * @param float $margin
     */
    public function setMarginPercent($margin)
    {
        $this->margin = $margin;
    }

    /**
     * @return GridDrawer
     */
    public function grid()
    {
        return $this->gridDrawer;
    }
}
