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

use Imagine\Coordinate\SizeInterface;
use Imagine\Filter\FilterInterface;
use Imagine\ImageInterface;

class Resize implements FilterInterface
{
    /**
     * @var SizeInterface
     */
    private $size;

    /**
     * Constructs Resize filter with given width and height
     *
     * @param SizeInterface $size
     */
    public function __construct(SizeInterface $size)
    {
        $this->size = $size;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return $image->resize($this->size);
    }
}
