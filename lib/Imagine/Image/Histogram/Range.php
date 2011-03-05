<?php

namespace Imagine\Image\Histogram;

use Imagine\Exception\OutOfBoundsException;

final class Range
{
    private $start;
    private $end;

    public function __construct($start, $end)
    {
        if ($end <= $start) {
            throw new OutOfBoundsException(sprintf(
                'Range end cannot be bigger than start, %d %d given '.
                'accordingly', $this->start, $this->end
            ));
        }

        $this->start = $start;
        $this->end   = $end;
    }

    public function contains($value)
    {
        return $value >= $this->start && $value < $this->end;
    }
}
