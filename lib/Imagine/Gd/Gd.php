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

class Gd implements GdInterface
{
    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\GdInterface::create()
     */
    public function create($width, $height)
    {
        return new Resource(imagecreatetruecolor($width, $height));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\GdInterface::load()
     */
    public function load($data)
    {
        return new Resource(imagecreatefromstring($data));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\GdInterface::open()
     */
    public function open($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $supported = array(
            'gif'  => IMG_GIF,
            'jpeg' => IMG_JPEG,
            'jpg'  => IMG_JPG,
            'png'  => IMG_PNG,
            'wbmp' => IMG_WBMP,
        );

        if (!isset($supported[$extension])) {
            return;
        }

        if (!(imagetypes() & $supported[$extension])) {
            return;
        }

        $extension = ('jpg' === $extension) ? 'jpeg' : $extension;

        return new Resource(call_user_func('imagecreatefrom'.$extension, $path));
    }
}
