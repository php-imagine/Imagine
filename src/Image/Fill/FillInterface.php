<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image\Fill;

use Imagine\Image\PointInterface;

/**
 * Interface for the fill.
 */
interface FillInterface
{
    /**
     * Gets color of the fill for the given position.
     *
     * @param \Imagine\Image\PointInterface $position
     *
     * @return \Imagine\Image\Palette\Color\ColorInterface
     */
    public function getColor(PointInterface $position);
}
