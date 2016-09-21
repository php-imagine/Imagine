<?php
namespace Imagine\Test\Filter\Chart;

use Imagine\Draw\LinePlotter;
use Imagine\Draw\LineStyle;
use Imagine\Filter\Chart\Data\DataSet;
use Imagine\Filter\Chart\GridDrawer;
use Imagine\Filter\Chart\LineChart;
use Imagine\Image\Box;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\ImagineInterface;
use Imagine\Test\ImagineTestCase;

abstract class AbstractChartTest extends ImagineTestCase
{
    public function getLineChart()
    {
        $imagine = $this->getImagine();

        $collage = $imagine->create(new Box(200, 200));

        $chart = new LineChart(
            $imagine,
            new LinePlotter(),
            new GridDrawer(),
            true,
            true,
            true,
            true
        );

        $chart->setMarginPercent(10);
        $chart->setPaddingPercent(10);
        $chart->grid()->setScaleStepX(2);
        $chart->grid()->setScaleStepY(10);
        $chart->setFontFace('tests/Imagine/Fixtures/font/Arial.ttf');
        $chart->setFontSize(10);

        $data = array();
        for($x=-6; $x <= 6; $x+=0.5) {$data[] = array($x, $x*$x);}
        $chart->addDataSet(
            new DataSet(
                $data,
                new LineStyle(
                    new RGB(new RGBPalette(), array(0, 128, 0), 100),
                    LineStyle::LINE_SOLID,
                    2,4
                )
            )
        );

        $data = array();
        for($x=-6; $x <= 6; $x+=0.5) {$data[] = array($x, - $x*$x);}
        $chart->addDataSet(
            new DataSet(
                $data,
                new LineStyle(
                    new RGB(new RGBPalette(), array(128, 0, 0), 100),
                    LineStyle::LINE_DASHED,
                    2, 4
                )
            )
        );

        $data = array();
        for($x=-6; $x <= 6; $x+=0.25) {$data[] = array($x, 5 * sin($x));}
        $chart->addDataSet(
            new DataSet(
                $data,
                new LineStyle(
                    new RGB(new RGBPalette(), array(0, 0, 128), 100),
                    LineStyle::LINE_DOTTED,
                    2,5
                )
            )
        );

        $chart->apply($collage);

        return $collage;
    }

    /**
     * @return ImagineInterface
     */
    abstract protected function getImagine();
}
