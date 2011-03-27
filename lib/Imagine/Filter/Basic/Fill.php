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

use Imagine\Fill\FillInterface;
use Imagine\Filter\FilterInterface;
use Imagine\ImageInterface;

class Fill implements FilterInterface
{
    /**
     * @var Imagine\Fill\FillInterface
     */
    private $fill;

    /**
     * @param Imagine\Fill\FillInterface $fill
     */
    public function __construct(FillInterface $fill)
    {
        $this->fill = $fill;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return $image->fill($this->fill);
    }
}
