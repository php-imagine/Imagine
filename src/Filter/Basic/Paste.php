<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter\Basic;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\PointInterface;

/**
 * A paste filter.
 */
class Paste implements FilterInterface
{
    /**
     * @var \Imagine\Image\ImageInterface
     */
    private $image;

    /**
     * @var \Imagine\Image\PointInterface
     */
    private $start;

    /**
     * How to paste the image, from 0 (fully transparent) to 100 (fully opaque).
     *
     * @var int
     */
    private $alpha;

    /**
     * Constructs a Paste filter with given ImageInterface to paste and x, y
     * coordinates of target position.
     *
     * @param \Imagine\Image\ImageInterface $image
     * @param \Imagine\Image\PointInterface $start
     * @param int $alpha how to paste the image, from 0 (fully transparent) to 100 (fully opaque)
     */
    public function __construct(ImageInterface $image, PointInterface $start, $alpha = 100)
    {
        $this->image = $image;
        $this->start = $start;
        $alpha = (int) round($alpha);
        if ($alpha < 0 || $alpha > 100) {
            throw new InvalidArgumentException(sprintf('The %1$s argument can range from %2$d to %3$d, but you specified %4$d.', '$alpha', 0, 100, $alpha));
        }
        $this->alpha = $alpha;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return $image->paste($this->image, $this->start, $this->alpha);
    }
}
