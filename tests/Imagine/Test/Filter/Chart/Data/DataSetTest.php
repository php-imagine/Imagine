<?php
namespace Imagine\Test\Filter\Chart\Data;

use Imagine\Filter\Chart\Data\DataSet;

class DataSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $data
     * @param bool $flipAxes
     * @param float $expectedMaxX
     * @param float $expectedMinX
     * @param float $expectedMaxY
     * @param float $expectedMinY
     *
     * @dataProvider providerDataSetSort
     */
    public function testDataSetSort(
        $data,
        $flipAxes,
        $expectedMaxX,
        $expectedMinX,
        $expectedMaxY,
        $expectedMinY
    )
    {
        $dataSet = new DataSet($data, null, $flipAxes);

        $this->assertEquals($expectedMaxX, $dataSet->getMaxX());
        $this->assertEquals($expectedMinX, $dataSet->getMinX());
        $this->assertEquals($expectedMaxY, $dataSet->getMaxY());
        $this->assertEquals($expectedMinY, $dataSet->getMinY());
    }

    public function providerDataSetSort()
    {
        return array(
            'ints all' => array(
                'data' => array(
                    array(-3, -11),
                    array(-2, -7),
                    array(-1, -3),
                    array(0, 0),
                    array(1, 3),
                    array(2, 7),
                    array(3, 11),
                ),
                'flipAxes' => false,
                'expectedMaxX' => 3,
                'expectedMinX' => -3,
                'expectedMaxY' => 11,
                'expectedMinY' => -11,
            ),
            'ints negative' => array(
                'data' => array(
                    array(-10, -11),
                    array(-8, -9),
                    array(-6, -7),
                    array(-4, -5),
                    array(-2, -3),
                    array(-1, -1),
                ),
                'flipAxes' => false,
                'expectedMaxX' => -1,
                'expectedMinX' => -10,
                'expectedMaxY' => -1,
                'expectedMinY' => -11,
            ),
            'ints positive' => array(
                'data' => array(
                    array(10, 11),
                    array(8, 9),
                    array(6, 7),
                    array(4, 5),
                    array(2, 3),
                    array(1, 1),
                ),
                'flipAxes' => false,
                'expectedMaxX' => 10,
                'expectedMinX' => 1,
                'expectedMaxY' => 11,
                'expectedMinY' => 1,
            ),
            'floats' => array(
                'data' => array(
                    array(-1.9, -11.5),
                    array(-1.7, -7.5),
                    array(-1.5, -3.5),
                    array(0.5, 0.5),
                    array(1.5, 3.5),
                    array(1.7, 7.5),
                    array(1.9, 11.5),
                ),
                'flipAxes' => false,
                'expectedMaxX' => 1.9,
                'expectedMinX' => -1.9,
                'expectedMaxY' => 11.5,
                'expectedMinY' => -11.5,
            ),
            'flip axes' => array(
                'data' => array(
                    array(-1.9, -11.5),
                    array(-1.7, -7.5),
                    array(-1.5, -3.5),
                    array(0.5, 0.5),
                    array(1.5, 3.5),
                    array(1.7, 7.5),
                    array(1.9, 11.5),
                ),
                'flipAxes' => true,
                'expectedMaxX' => 11.5,
                'expectedMinX' => -11.5,
                'expectedMaxY' => 1.9,
                'expectedMinY' => -1.9,
            ),
        );
    }
}
