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

final class Center implements PointInterface
{
    /**
     * @var Imagine\Image\BoxInterface
     */
    private $box;

    /**
     * Constructs coordinate with size instantce, it needs to be relative to
     *
     * @param Imagine\Image\BoxInterface $size
     */
    public function __construct(BoxInterface $box)
    {
        $this->box = $box;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\PointInterface::getX()
     */
    public function getX()
    {
        return ceil($this->box->getWidth() / 2);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\PointInterface::getY()
     */
    public function getY()
    {
        return ceil($this->box->getHeight() / 2);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\PointInterface::in()
     */
    public function in(BoxInterface $box)
    {
        return $this->getX() < $box->getWidth() && $this->getY() < $box->getHeight();
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\PointInterface::__toString()
     */
    public function __toString()
    {
        return sprintf('(%d, %d)', $this->getX(), $this->getY());
    }
}
