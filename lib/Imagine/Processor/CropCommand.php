<?php

namespace Imagine\Processor;

use Imagine\Image;

class CropCommand implements Command {
    
    protected $x;
    protected $y;
    protected $width;
    protected $height;
    protected $resource;
    protected $initial = array();

    public function __construct($x, $y, $width, $height) {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
    }

    public function process(Image $image) {
        $srcImage = $image->getResource();
        $destImage = imagecreatetruecolor($this->width, $this->height);
        if ( ! imagecopy($destImage, $srcImage, 0, 0, $this->x, $this->y, $this->width, $this->height)) {
            throw new \RuntimeException('Could not resize the image');
        }
        imagedestroy($srcImage);
        $this->initial = array(
            'width' => $image->getWidth(),
            'height' => $image->getHeight(),
            'content' => $image->getContent(),
        );
        $image->setWidth($this->width);
        $image->setHeight($this->height);
        $this->resource = $destImage;
    }

    public function restore(Image $image) {
        $image->setContent($this->initial['content']);
        $image->setWidth($this->initial['width']);
        $image->setHeight($this->initial['height']);
        $this->initial = array();
        $this->resource = null;
    }

    public function getImageResource() {
        return $this->resource;
    }

}
