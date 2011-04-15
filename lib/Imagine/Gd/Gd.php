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

use Imagine\Image\BoxInterface;

class Gd implements GdInterface
{
    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\GdInterface::create()
     */
    public function create(BoxInterface $size)
    {
        return $this->wrap(imagecreatetruecolor($size->getWidth(), $size->getHeight()));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Gd\GdInterface::load()
     */
    public function load($data)
    {
        return $this->wrap(imagecreatefromstring($data));
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

        return $this->wrap(call_user_func('imagecreatefrom'.$extension, $path));
    }

    /**
     * Checks if the resource was created successfuly and wraps it in
     * Imagine\Gd\ResourceInterface, otherwise returns null
     *
     * @param mixed $resource
     *
     * @return mixed
     */
    private function wrap($resource)
    {
        return (false === $resource) ? null : new Resource($resource);
    }
}
