<?php
namespace Imagine\Test\Filter\Chart;

use Imagine\Filter\Chart\ChartConfig;
use Imagine\Filter\Chart\Data\DataSet;
use Imagine\Image\Box;
use Imagine\Image\Point;

class ChartConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $dataSets
     * @param bool $fitView
     * @param float $marginPercent
     * @param float $paddingPercent
     * @param bool $labelAxes
     * @param int $fontSize
     * @param array $expectedXRange
     * @param array $expectedYRange
     * @param float $expectedScaleFactorX
     * @param float  $expectedScaleFactorY
     * @param Point $expectedOrigin
     *
     * @dataProvider providerChartConfig
     */
    public function testChartConfig(
        array $dataSets,
        $fitView,
        $marginPercent,
        $paddingPercent,
        $labelAxes,
        $fontSize,
        array $expectedXRange,
        array $expectedYRange,
        $expectedScaleFactorX,
        $expectedScaleFactorY,
        $expectedOrigin
    ) {
        $imageMock = $this->getMock('Imagine\\Image\\ImageInterface');
        $imageMock->expects($this->any())->method('getSize')->willReturn(new Box(100,100));

        $fontMock = $this->getMockBuilder('Imagine\\Image\\FontInterface')->disableOriginalConstructor()->getMock();
        $fontMock->expects($this->any())->method('getSize')->with()->willReturn($fontSize);

        $config = new ChartConfig(
                $imageMock,
                $dataSets,
                $fitView,
                $marginPercent,
                $paddingPercent,
                $labelAxes,
                $fontMock
        );

        $this->assertEquals($expectedXRange[0], $config->getNegativeXRange());
        $this->assertEquals($expectedXRange[1], $config->getPositiveXRange());
        $this->assertEquals($expectedYRange[0], $config->getNegativeYRange());
        $this->assertEquals($expectedYRange[1], $config->getPositiveYRange());

        $this->assertEquals($expectedScaleFactorX, $config->getScaleFactorX());
        $this->assertEquals($expectedScaleFactorY, $config->getScaleFactorY());

        $this->assertEquals($expectedOrigin, $config->getOrigin());

    }

    public function providerChartConfig()
    {
        return array(
            'centered view no margin no padding no label' => array(
                'datasets' => array(
                    new DataSet(array(
                        array(0,-1),
                        array(1,-2),
                        array(2,-3),
                        array(3,-4),
                        array(5,-10),
                    )),
                    new DataSet(array(
                        array(0, 10),
                        array(-1, 2),
                        array(-2, 3),
                        array(-3, 4),
                        array(-10, 6),
                    ))
                ),
                'fitView' => false,
                'marginPercent' => 0.0,
                'paddingPercent' => 0.0,
                'labelAxes' => false,
                'fontSize' => 10,
                'expectedXRange' => array(-10, 10),
                'expectedYRange' => array(-10, 10),
                'expectedScaleFactorX' => 5.0,
                'expectedScaleFactorY' => 5.0,
                'expectedOrigin' => new Point(50,50)
            ),

            'fit view no margin no padding no label' => array(
                'datasets' => array(
                    new DataSet(array(
                        array(0,-1),
                        array(1,-2),
                        array(2,-3),
                        array(3,-4),
                        array(5,-10),
                    )),
                    new DataSet(array(
                        array(0, 10),
                        array(-1, 2),
                        array(-2, 3),
                        array(-3, 4),
                        array(-10, 6),
                    ))
                ),
                'fitView' => true,
                'marginPercent' => 0.0,
                'paddingPercent' => 0.0,
                'labelAxes' => false,
                'fontSize' => 10,
                'expectedXRange' => array(-10, 5),
                'expectedYRange' => array(-10, 10),
                'expectedScaleFactorX' => 6.666666666666667,
                'expectedScaleFactorY' => 5.0,
                'expectedOrigin' => new Point(66.666666666666671,50)
            ),

            'centered view with margin no padding no label' => array(
                'datasets' => array(
                    new DataSet(array(
                        array(0,-1),
                        array(1,-2),
                        array(2,-3),
                        array(3,-4),
                        array(5,-10),
                    )),
                    new DataSet(array(
                        array(0, 10),
                        array(-1, 2),
                        array(-2, 3),
                        array(-3, 4),
                        array(-10, 6),
                    ))
                ),
                'fitView' => false,
                'marginPercent' => 10.0,
                'paddingPercent' => 0.0,
                'labelAxes' => false,
                'fontSize' => 10,
                'expectedXRange' => array(-10, 10),
                'expectedYRange' => array(-10, 10),
                'expectedScaleFactorX' => 4.5,
                'expectedScaleFactorY' => 4.5,
                'expectedOrigin' => new Point(50,50)
            ),

            'centered view no margin with padding no label' => array(
                'datasets' => array(
                    new DataSet(array(
                        array(0,-1),
                        array(1,-2),
                        array(2,-3),
                        array(3,-4),
                        array(5,-10),
                    )),
                    new DataSet(array(
                        array(0, 10),
                        array(-1, 2),
                        array(-2, 3),
                        array(-3, 4),
                        array(-10, 6),
                    ))
                ),
                'fitView' => false,
                'marginPercent' => 0.0,
                'paddingPercent' => 10.0,
                'labelAxes' => false,
                'fontSize' => 10,
                'expectedXRange' => array(-11.111111111111111, 11.111111111111111),
                'expectedYRange' => array(-11.111111111111111, 11.111111111111111),
                'expectedScaleFactorX' => 4.5,
                'expectedScaleFactorY' => 4.5,
                'expectedOrigin' => new Point(50,50)
            ),

            'centered view no margin no padding with label' => array(
                'datasets' => array(
                    new DataSet(array(
                        array(0,-1),
                        array(1,-2),
                        array(2,-3),
                        array(3,-4),
                        array(5,-10),
                    )),
                    new DataSet(array(
                        array(0, 10),
                        array(-1, 2),
                        array(-2, 3),
                        array(-3, 4),
                        array(-10, 6),
                    ))
                ),
                'fitView' => false,
                'marginPercent' => 0.0,
                'paddingPercent' => 0.0,
                'labelAxes' => true,
                'fontSize' => 10,
                'expectedXRange' => array(-10, 10),
                'expectedYRange' => array(-10, 10),
                'expectedScaleFactorX' => 2.75,
                'expectedScaleFactorY' => 2.75,
                'expectedOrigin' => new Point(50,50)
            )
        );
    }
}
