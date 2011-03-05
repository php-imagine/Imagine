<?php

namespace Imagine\Image\Histogram;

final class Bucket implements \Countable
{
    private $range;
    private $count;

    public function __construct(Range $range, $count = 0)
    {
        $this->range = $range;
        $this->count = $count;
    }

    public function add($value)
    {
        if ($this->range->contains($value)) {
            $this->count++;
        }
    }

    public function count()
    {
        return $this->count;
    }
}
