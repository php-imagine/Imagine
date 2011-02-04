<?php

namespace Imagine\Gd;

use Imagine\ImageFactoryInterface;

class ImageFactory implements ImageFactoryInterface
{
    public function create($width, $height)
    {
        return new BlankImage($width, $height);
    }

    public function open($path)
    {
        return new FileImage($path);
    }
}
