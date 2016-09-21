<?php
namespace Imagine\Test\Imagick;

use Imagine\Imagick\Imagine;
use Imagine\Test\Filter\Chart\AbstractChartTest;

class ChartTest extends AbstractChartTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
        }
    }

    public function testLineChart()
    {
        $lineChart = $this->getLineChart();
        $expected = $this->getImagine()->open('tests/Imagine/Fixtures/expectedLineChartTestImagick.png');

        self::assertImageEquals($expected, $lineChart, 'Chart image not generated as expected');
    }

    protected function getImagine()
    {
        return new Imagine();
    }
}
