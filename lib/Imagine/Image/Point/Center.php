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
     * @var Imagine\Image\BoxInterface
     */
    private $box;

    /**
     * Constructs coordinate with size instance, it needs to be relative to
     *
     * @param Imagine\Image\BoxInterface $size
     */
    public function __construct(BoxInterface $box)
    {
        $this->box = $box;
        parent::__construct($this->calculateX(), $this->calculateY());
    }

    private function calculateX()
    {
        return ceil($this->box->getWidth() / 2);
    }

    private function calculateY()
    {
        return ceil($this->box->getHeight() / 2);
    }
}
