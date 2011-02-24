<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Mask\Gradient;

use Imagine\Mask\MaskInterface;
use Imagine\PointInterface;

abstract class Linear implements MaskInterface
{
    /**
     * @var integer
     */
    private $length;

    /**
     * @var integer
     */
    private $start;

    /**
     * @var integer
     */
    private $end;

    /**
     * Constructs a linear gradient with overal gradient length, and start and
     * end shades, which default to 0 and 255 accordingly
     *
     * @param integer $length
     * @param integer $start
     * @param integer $end
     */
    public function __construct($length, $start = 0, $end = 255)
    {
        $this->length = $length;
        $this->start = $start;
        $this->end   = $end;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Mask.MaskInterface::getShade()
     */
    public function getShade(PointInterface $position)
    {
        $l = $this->getDistance($position);

        if ($l >= $this->length) {
            return $this->end;
        }

        if ($l <= 0) {
            return $this->start;
        }

        return abs(round(($this->end - $this->start) * $l / $this->length));
    }

    /**
     * Get the distance of the position relative to the begining of the gradient
     *
     * @param Imagine\PointInterface $position
     *
     * @return integer
     */
    abstract protected function getDistance(PointInterface $position);
}
