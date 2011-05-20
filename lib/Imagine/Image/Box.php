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

        $this->width  = (int) $width;
        $this->height = (int) $height;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\BoxInterface::getWidth()
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\BoxInterface::getHeight()
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\BoxInterface::scale()
     */
    public function scale($ratio)
    {
        return new Box(round($ratio * $this->width), round($ratio * $this->height));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\BoxInterface::increase()
     */
    public function increase($size)
    {
        return new Box((int) $size + $this->width, (int) $size + $this->height);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\BoxInterface::contains()
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
     * @see Imagine\Image\BoxInterface::square()
     */
    public function square()
    {
        return $this->width * $this->height;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\BoxInterface::position()
     */
    public function position($a, $b)
    {
        $positionOf = function(array $sides) use ($a, $b) {
            return in_array($a, $sides) ? $a : (in_array($b, $sides) ? $b : null);
        };

        $x = $positionOf(array(self::LEFT, self::RIGHT, self::CENTER));
        $y = $positionOf(array(self::TOP, self::BOTTOM, self::MIDDLE));

        if (null === $x || null === $y) {
            throw new InvalidArgumentException(
                sprintf(
                    'Allowed positions are "%s", "%s" and "%s" given',
                    implode(
                        '", "',
                        array(
                            self::TOP, self::BOTTOM, self::MIDDLE,
                            self::LEFT, self::RIGHT, self::CENTER
                        )
                    ),
                    $a, $b
                )
            );
        }

        $values = array(
            self::TOP    => 0,
            self::LEFT   => 0,
            self::BOTTOM => $this->height,
            self::RIGHT  => $this->width,
            self::MIDDLE => round($this->height / 2),
            self::CENTER => round($this->width / 2),
        );

        return new Point($values[$x], $values[$y]);
    }


    /**
     * (non-PHPdoc)
     * @see Imagine\Image\BoxInterface::__toString()
     */
    public function __toString()
    {
        return sprintf('%dx%d px', $this->width, $this->height);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\BoxInterface::widen()
     */
    public function widen($width)
    {
        return $this->scale($width / $this->width);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\BoxInterface::heighten()
     */
    public function heighten($height)
    {
        return $this->scale($height / $this->height);
    }
}
