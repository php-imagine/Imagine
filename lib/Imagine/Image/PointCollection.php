<?php
namespace Imagine\Image;

use Imagine\Exception\InvalidArgumentException;

class PointCollection
{
    /** @var  PointInterface[] */
    private $points;

    private $iterator = 0;

    /**
     * @param PointInterface[] $points
     */
    public function __construct(array $points)
    {
        foreach($points as $point) {
            if (false === $point instanceof PointInterface) {
                throw new InvalidArgumentException('All elements in collection must be instance of PointInterface.');
            }

            $this->points[] = $point;
        }
    }

    public function reset() {
        $this->iterator = 0;
    }

    /**
     * @return bool
     */
    public function isFinished() {
        return !array_key_exists($this->iterator + 1, $this->points);
    }

    /**
     * @return array|bool
     */
    public function getNextPair()
    {
       if (false === $this->isFinished()) {
           $pair = array($this->points[$this->iterator], $this->points[$this->iterator + 1]);
           $this->iterator++;
           return $pair;
       }

       return false;
    }

}
