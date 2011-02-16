<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Cartesian\Coordinate;

use Imagine\Cartesian\CoordinateInterface;
use Imagine\Cartesian\SizeInterface;

final class Center implements CoordinateInterface
{
    /**
     * @var SizeInterface
     */
    private $box;

    /**
     * Constructs coordinate with size instantce, it needs to be relative to
     *
     * @param SizeInterface $size
     */
    public function __construct(SizeInterface $box)
    {
        $this->box = $box;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Cartesian.CoordinateInterface::getX()
     */
    public function getX()
    {
        return ceil($this->box->getWidth() / 2);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Cartesian.CoordinateInterface::getY()
     */
    public function getY()
    {
        return ceil($this->box->getHeight() / 2);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Cartesian.CoordinateInterface::in()
     */
    public function in(SizeInterface $box)
    {
        return $this->getX() < $box->getWidth() && $this->getY() < $box->getHeight();
    }
}
