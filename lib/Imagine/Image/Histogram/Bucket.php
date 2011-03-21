<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image\Histogram;

final class Bucket implements \Countable
{
    /**
     * @var Imagine\Image\Histogram\Range
     */
    private $range;

    /**
     * @var integer
     */
    private $count;

    /**
     * @param Imagine\Image\Histogram\Range $range
     * @param integer                       $count
     */
    public function __construct(Range $range, $count = 0)
    {
        $this->range = $range;
        $this->count = $count;
    }

    /**
     * @param integer $value
     */
    public function add($value)
    {
        if ($this->range->contains($value)) {
            $this->count++;
        }
    }

    /**
     * (non-PHPdoc)
     * @see Countable::count()
     */
    public function count()
    {
        return $this->count;
    }
}
