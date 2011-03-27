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
use Imagine\ImageInterface;

class ApplyMask implements FilterInterface
{
    /**
     * @var Imagine\ImageInterface
     */
    private $mask;

    /**
     * @param Imagine\ImageInterface $mask
     */
    public function __construct(ImageInterface $mask)
    {
        $this->mask = $mask;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return $image->applyMask($this->mask);
    }
}
