<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Utils;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;

class Matrix
{
    protected $elements = array();

    /** @var int */
    protected $width;

    /** @var int */
    protected $height;

    /**
     * The given $elements get arranged as follows: The elements will be set from left to right in a row until the
     * row is full. Then, the next line begins alike and so on.
     *
     * @param $width
     * @param $height
     * @param array $elements
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    public function __construct($width, $height, $elements = array())
    {
        if ($width < 1) {
            throw new InvalidArgumentException('width has to be > 0');
        }

        if ($height < 1) {
            throw new InvalidArgumentException('height has to be > 0');
        }

        if ($width * $height < count($elements)) {
            throw new InvalidArgumentException('there are more provided elements than space in the matrix');
        }

        $this->width    = $width;
        $this->height   = $height;
        $this->elements = $elements;

        if (count($this->elements) < $this->width * $this->height) {
            $this->elements = array_merge(
                $this->elements,
                array_fill(count($this->elements), $width * $height - count($this->elements), 0)
            );
        }
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setElementAt($x, $y, $value)
    {
        $this->elements[$this->calculatePosition($x, $y)] = $value;
    }

    public function getElementAt($x, $y)
    {
        return $this->elements[$this->calculatePosition($x, $y)];
    }

    protected function calculatePosition($x, $y)
    {
        if (0 > $x || 0 > $y || $this->width <= $x || $this->height <= $y) {
            throw new OutOfBoundsException(sprintf('There is no position (%s, %s) in this matrix', $x, $y));
        }

        return $y * $this->height + $x;
    }
}