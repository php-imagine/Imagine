<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine;

use Imagine\Exception\InvalidArgumentException;

final class Box implements BoxInterface
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
     * @see Imagine\BoxInterface::getWidth()
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\BoxInterface::getHeight()
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\BoxInterface::scale()
     */
    public function scale($ratio)
    {
        return new Box($ratio * $this->width, $ratio * $this->height);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\BoxInterface::increase()
     */
    public function increase($size)
    {
        return new Box($size + $this->width, $size + $this->height);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\BoxInterface::contains()
     */
    public function contains(BoxInterface $box, PointInterface $start = null)
    {
        $start = $start ? $start : new Point(0, 0);

        return $start->in($this) &&
            $this->width >= $box->getWidth() + $start->getX() &&
            $this->height >= $box->getHeight() + $start->getY();
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\BoxInterface::__toString()
     */
    public function __toString()
    {
        return sprintf('%dx%d px', $this->width, $this->height);
    }
}
