<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter;

use Imagine\Image\ImagineInterface;

abstract class ImagineAware implements FilterInterface
{
    /**
     * An ImagineInterface instance.
     *
     * @var ImagineInterface
     */
    protected $imagine;

    /**
     * Set ImagineInterface instance.
     *
     * @param ImagineInterface $imagine An ImagineInterface instance
     */
    public function setImagine(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }
}