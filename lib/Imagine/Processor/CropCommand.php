<?php

namespace Imagine\Processor;

use Imagine\Image;

class CropCommand extends AbstractCommand {
    
    protected $x;
    protected $y;
    protected $width;
    protected $height;

    public function __construct($x, $y, $width, $height) {
        $this->x = (int) $x;
        $this->y = (int) $y;
        $this->width = (int) $width;
        $this->height = (int) $height;
    }

    public function _process(Image $image) {
        $srcImage = $image->getResource();
        $destImage = imagecreatetruecolor($this->width, $this->height);
        if ( ! imagecopy($destImage, $srcImage, 0, 0, $this->x, $this->y, $this->width, $this->height)) {
            throw new \RuntimeException('Could not resize the image');
        }
        imagedestroy($srcImage);
        $image->setWidth($this->width);
        $image->setHeight($this->height);
        return $destImage;
    }

    public function _restore(Image $image, array $snapshot) {
        $image->setContent($snapshot['content']);
        $image->setWidth($snapshot['width']);
        $image->setHeight($snapshot['height']);
    }


}
