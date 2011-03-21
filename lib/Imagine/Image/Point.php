<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

use Imagine\Exception\InvalidArgumentException;

final class Point implements PointInterface
{
    /**
     * @var integer
     */
    private $x;

    /**
     * @var integer
     */
    private $y;

    /**
     * Constructs a point of coordinates
     *
     * @param integer $x
     * @param integer $y
     *
     * @throws Imagine\Exception\InvalidArgumentException
     */
    public function __construct($x, $y)
    {
        if ($x < 0 || $y < 0) {
            throw new InvalidArgumentException(
                'A coordinate cannot be positioned ouside of a bounding box'
            );
        }

        $this->x = $x;
        $this->y = $y;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\PointInterface::getX()
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\PointInterface::getY()
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\PointInterface::in()
     */
    public function in(BoxInterface $box)
    {
        return $this->x < $box->getWidth() && $this->y < $box->getHeight();
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\PointInterface::__toString()
     */
    public function __toString()
    {
        return sprintf('(%d, %d)', $this->x, $this->y);
    }
}
