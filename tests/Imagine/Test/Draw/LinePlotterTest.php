<?php
namespace Imagine\Test\Draw;

use Imagine\Draw\LinePlotter;
use Imagine\Draw\LineStyle;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\Point;
use Imagine\Image\PointCollection;
use Imagine\Image\PointInterface;

class LinePlotterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param PointInterface $point1
     * @param PointInterface $point2
     * @param LineStyle $lineStyle
     * @param $expectedDrawerFunction
     * @param $expectedCallCount
     *
     * @dataProvider providerPlot
     */
    public function testPlot(
        PointInterface $point1,
        PointInterface $point2,
        LineStyle $lineStyle = null,
        $expectedDrawerFunction,
        $expectedCallCount
    ) {
        $mockDrawer = $this->getMock('Imagine\\Draw\\DrawerInterface');

        $plotter = new LinePlotter($mockDrawer);

        $mockDrawer->expects($this->exactly($expectedCallCount))->method($expectedDrawerFunction);
        $plotter->plot($point1, $point2, $lineStyle);
    }

    public function providerPlot()
    {
        return array(
            'default style' => array(
                'point1' => new Point(0,0),
                'point2' => new Point(10,10),
                'lineStyle' => null,
                'expectedDrawerFunction' => 'line',
                'expectedCallCount' => 1
            ),
            'dashed style' => array(
                'point1' => new Point(0,0),
                'point2' => new Point(0,20),
                'lineStyle' => new LineStyle(new RGB(new RGBPalette(), array(0, 0, 0), 100), LineStyle::LINE_DASHED, 1, 2),
                'expectedDrawerFunction' => 'line',
                'expectedCallCount' => 5
            ),
            'dotted style' => array(
                'point1' => new Point(0,0),
                'point2' => new Point(0,20),
                'lineStyle' => new LineStyle(new RGB(new RGBPalette(), array(0, 0, 0), 100), LineStyle::LINE_DOTTED, 1, 2),
                'expectedDrawerFunction' => 'ellipse',
                'expectedCallCount' => 11
            )
        );
    }

    /**
     * @param PointCollection $collection
     * @param LineStyle $lineStyle
     * @param $expectedDrawerFunction
     * @param $expectedCallCount
     *
     * @dataProvider providerPlotCollection
     */
    public function testPlotCollection(
        PointCollection $collection,
        LineStyle $lineStyle = null,
        $expectedDrawerFunction,
        $expectedCallCount
    ) {
        $mockDrawer = $this->getMock('Imagine\\Draw\\DrawerInterface');

        $plotter = new LinePlotter($mockDrawer);

        $mockDrawer->expects($this->exactly($expectedCallCount))->method($expectedDrawerFunction);
        $plotter->plotCollection($collection, $lineStyle);
    }

    public function providerPlotCollection()
    {
        return array(
            'default style' => array(
                'collection' => new PointCollection(array(new Point(0,0), new Point(1,1), new Point(2,2), new Point(3,3), new Point(4,4))),
                'lineStyle' => null,
                'expectedDrawerFunction' => 'line',
                'expectedCallCount' => 4
            )
        );
    }

    /**
     * @param PointInterface $point
     * @param $text
     *
     * @dataProvider providerLabel
     */
    public function testLabel(PointInterface $point, $text)
    {
        $mockDrawer = $this->getMock('Imagine\\Draw\\DrawerInterface');
        $mockFont = $this->getMockBuilder('Imagine\\Image\\AbstractFont')->disableOriginalConstructor()->getMock();

        $plotter = new LinePlotter($mockDrawer);

        $mockDrawer->expects($this->once())
            ->method('text')
            ->with($text, $mockFont, $point);

        $plotter->label($point, $text, $mockFont);
    }

    public function providerLabel()
    {
        return array(
            array('point' => new Point(0,0), 'text' => 'ABCDEFG'),
            array('point' => new Point(0,1), 'text' => 'IJKLMNO'),
            array('point' => new Point(0,2), 'text' => 'PQRSTUV'),
            array('point' => new Point(0,3), 'text' => 'WXYZ123'),
        );
    }
}
