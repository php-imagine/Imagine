<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image\Point;

use Imagine\Image\BoxInterface;
use Imagine\Image\PointInterface;
use Imagine\Image\AbstractPoint;

final class Center extends AbstractPoint
{
    /**
     * Constructs coordinate with size instance, it needs to be relative to
     *
     * @param Imagine\Image\BoxInterface $size
     */
    public function __construct(BoxInterface $box)
    {
        parent::__construct(
            $this->middle($box->getWidth()),
            $this->middle($box->getHeight())
        );
    }

    /**
     * Calculates the middle point of a given length
     *
     * @param integer $length
     *
     * @return integer
     */
    private function middle($length)
    {
        return ceil($length / 2);
    }
}
