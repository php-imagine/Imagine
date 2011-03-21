<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Fill\Gradient;

use Imagine\Image\Color;
use Imagine\Image\PointInterface;
use Imagine\Fill\FillInterface;

abstract class Linear implements FillInterface
{
    /**
     * @var integer
     */
    private $length;

    /**
     * @var Imagine\Image\Color
     */
    private $start;

    /**
     * @var Imagine\Image\Color
     */
    private $end;

    /**
     * Constructs a linear gradient with overal gradient length, and start and
     * end shades, which default to 0 and 255 accordingly
     *
     * @param integer       $length
     * @param Imagine\Image\Color $start
     * @param Imagine\Image\Color $end
     */
    public function __construct($length, $start, $end)
    {
        $this->length = $length;
        $this->start  = $start;
        $this->end    = $end;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Fill\FillInterface::getShade()
     */
    public function getColor(PointInterface $position)
    {
        $l = $this->getDistance($position);

        if ($l >= $this->length) {
            return $this->end;
        }

        if ($l < 0) {
            return $this->start;
        }

        $color = new Color(array(
                (int) min(255, min($this->start->getRed(), $this->end->getRed()) + round(abs($this->end->getRed() - $this->start->getRed()) * $l / $this->length)),
                (int) min(255, min($this->start->getGreen(), $this->end->getGreen()) + round(abs($this->end->getGreen() - $this->start->getGreen()) * $l / $this->length)),
                (int) min(255, min($this->start->getBlue(), $this->end->getBlue()) + round(abs($this->end->getBlue() - $this->start->getBlue()) * $l / $this->length)),
            ),
            (int) min(100, min($this->start->getAlpha(), $this->end->getAlpha()) + round(abs($this->end->getAlpha() - $this->start->getAlpha()) * $l / $this->length))
        );

        return $color;
    }

    /**
     * Get the distance of the position relative to the begining of the gradient
     *
     * @param Imagine\Image\PointInterface $position
     *
     * @return integer
     */
    abstract protected function getDistance(PointInterface $position);
}
