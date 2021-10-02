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

/**
 * A point class that allows negative values of coordinates.
 */
final class PointSigned implements PointInterface
{
    /**
     * @var int
     */
    private $x;

    /**
     * @var int
     */
    private $y;

    /**
     * Constructs a point of coordinates.
     *
     * @param int $x
     * @param int $y
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\PointInterface::getX()
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\PointInterface::getY()
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\PointInterface::in()
     */
    public function in(BoxInterface $box)
    {
        return $this->x >= 0 && $this->x < $box->getWidth() && $this->y >= 0 && $this->y < $box->getHeight();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\PointInterface::move()
     */
    public function move($amount)
    {
        return new self($this->x + $amount, $this->y + $amount);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\PointInterface::__toString()
     */
    public function __toString()
    {
        return sprintf('(%d, %d)', $this->x, $this->y);
    }
}
