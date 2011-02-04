<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

use Imagine\ImageFactoryInterface;

class ImageFactory implements ImageFactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see Imagine.ImageFactoryInterface::create()
     */
    public function create($width, $height)
    {
        return new BlankImage($width, $height);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageFactoryInterface::open()
     */
    public function open($path)
    {
        return new FileImage($path);
    }
}
