<?php
namespace Imagine\Filter\Chart\Data;

use Imagine\Exception\InvalidArgumentException;

class DataSet
{
    /** @var array */
    private $dataByX = array();

    /** @var  float|int */
    private $minX;

    /** @var  float|int */
    private $maxY;

    /** @var  float|int */
    private $minY;

    /** @var  float|int */
    private $maxX;

    private $lineStyle;

    /**
     * @param array $data
     * @param $lineStyle
     * @param bool $flipAxes
     */
    public function __construct(array $data, $lineStyle = null, $flipAxes = false)
    {
        $this->sortIncomingData($data, $flipAxes);
        $this->lineStyle = $lineStyle;
    }

    /**
     * @return mixed
     */
    public function getLineStyle()
    {
        return $this->lineStyle;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->dataByX;
    }

    public function Length()
    {
        return count($this->dataByX);
    }

    /**
     * @return mixed
     */
    public function getMaxX()
    {
        return $this->maxX;
    }

    /**
     * @return mixed
     */
    public function getMinX()
    {
        return $this->minX;
    }

    /**
     * @return mixed
     */
    public function getMaxY()
    {
        return $this->maxY;
    }

    /**
     * @return mixed
     */
    public function getMinY()
    {
        return $this->minY;
    }

    private function sortIncomingData($data, $flipAxes = false)
    {
        $minX = $minY = INF;
        $maxX = $maxY = -INF;

        foreach($data as $pair) {
            if (count($pair) < 2) {
                continue;
            }

            $x = array_key_exists('x', $pair) ? (float) $pair['x'] : (float) $pair[0];
            $y = array_key_exists('y', $pair) ? (float) $pair['y'] : (float) $pair[1];

            if (true === $flipAxes) {
                $tmp = $x;
                $x = $y;
                $y = $tmp;
            }

            $this->dataByX[] = new DataPoint($x, $y);

            if ($x <= $minX) { $minX = $x;}
            if ($y <= $minY) { $minY = $y;}
            if ($x >= $maxX) { $maxX = $x;}
            if ($y >= $maxY) { $maxY = $y;}
        }

        if (count($this->dataByX) > 0 ) {
            $this->minX = $minX;
            $this->minY = $minY;
            $this->maxX = $maxX;
            $this->maxY = $maxY;

            usort($this->dataByX, function (DataPoint $p1, DataPoint $p2) {
                if ($p1->getX() === $p2->getX()) { return 0; } elseif ($p1->getX() > $p2->getX()) { return 1;}
                return -1;
            });
        } else {
            throw new InvalidArgumentException("No valid data pair in set");
        }
    }

}
