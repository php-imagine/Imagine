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

use Imagine\Filter\FilterInterface;
use Imagine\Image\Fill\FillInterface;
use Imagine\Image\ImageInterface;

/**
 * A fill filter.
 */
class Fill implements FilterInterface
{
    /**
     * @var \Imagine\Image\Fill\FillInterface
     */
    private $fill;

    /**
     * @param \Imagine\Image\Fill\FillInterface $fill
     */
    public function __construct(FillInterface $fill)
    {
        $this->fill = $fill;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return $image->fill($this->fill);
    }
}
