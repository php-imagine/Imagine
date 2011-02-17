<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Coordinate;

use Imagine\Exception\InvalidArgumentException;

final class Size implements SizeInterface
{
    /**
     * @var integer
     */
    private $width;

    /**
     * @var integer
     */
    private $height;

    /**
     * Constructs the Size with given width and height
     *
     * @param integer $width
     * @param integer $height
     *
     * @throws InvalidArgumentException
     */
    public function __construct($width, $height)
    {
        if ($height < 1 || $width < 1) {
            throw new InvalidArgumentException(sprintf(
                'Length of either side cannot be 0 or negative, current size '.
                'is %sx%s', $width, $height
            ));
        }

        $this->width  = $width;
        $this->height = $height;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Coordinate.SizeInterface::getWidth()
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Coordinate.SizeInterface::getHeight()
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Coordinate.SizeInterface::scale()
     */
    public function scale($ratio)
    {
        return new Size($ratio * $this->width, $ratio * $this->height);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Coordinate.SizeInterface::contains()
     */
    public function contains(SizeInterface $box, CoordinateInterface $start = null)
    {
        $start = $start ? $start : new Coordinate(0, 0);

        return $start->in($this) &&
            $this->width >= $box->getWidth() + $start->getX() &&
            $this->height >= $box->getHeight() + $start->getY();
    }
}
