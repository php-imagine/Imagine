<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Coordinate\Coordinate;

use Imagine\Coordinate\CoordinateInterface;
use Imagine\Coordinate\SizeInterface;

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
     * @see Imagine\Coordinate.CoordinateInterface::getX()
     */
    public function getX()
    {
        return ceil($this->box->getWidth() / 2);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Coordinate.CoordinateInterface::getY()
     */
    public function getY()
    {
        return ceil($this->box->getHeight() / 2);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Coordinate.CoordinateInterface::in()
     */
    public function in(SizeInterface $box)
    {
        return $this->getX() < $box->getWidth() && $this->getY() < $box->getHeight();
    }
}
