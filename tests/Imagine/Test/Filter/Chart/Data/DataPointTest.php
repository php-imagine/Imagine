<?php
namespace Imagine\Test\Filter\Chart\Data;

use Imagine\Filter\Chart\Data\DataPoint;

class DataPointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $x
     * @param $y
     *
     * @dataProvider  providerDataPoint
     */
    public function testDataPoint($x, $y)
    {
        $dataPoint = new DataPoint($x, $y);
        $this->assertEquals($x, $dataPoint->getX());
        $this->assertEquals($y, $dataPoint->getY());
    }

    public function providerDataPoint()
    {
        return array(
            array(
              'x' => 1,
              'y' => 2
            ),
            array(
                'x' => 0.005,
                'y' => 5000.0
            )
        );
    }
}
