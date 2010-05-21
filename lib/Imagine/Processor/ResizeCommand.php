<?php

namespace Imagine\Processor;

use Imagine\Image;

class ResizeCommand implements Command {

    protected $width;
    protected $height;
    protected $resource;
    protected $initial = array();

    public function __construct($width, $height) {
        $this->width = (true === $width) ? true : (int) $width;
        $this->height = (true === $height) ? true : (int) $height;
    }

    public function process(Image $image) {
        $srcImage = $image->getResource();
        $this->adjustSize($image);
        $destImage = imagecreatetruecolor($this->width, $this->height);
        if ( ! imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $this->width, $this->height, $image->getWidth(), $image->getHeight())) {
            throw new \RuntimeException('Could not resize the image');
        }
        imagedestroy($srcImage);
        $this->initial = array(
            'content' => $image->getContent(),
            'width' => $image->getWidth(),
            'height' => $image->getHeight(),
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

    // @todo: looks kinda ugly, need to find a better way to calculate side ratios
    private function adjustSize(Image $image) {
        $mainSide = false;
        if (true === $this->width) {
            $mainSide = 'height';
        } elseif (true === $this->height) {
            $mainSide = 'width';
        }
        if (false !== $mainSide) {
            $otherSide = ($mainSide === 'width') ? 'height' : 'width';
            $ratio = $image->{'get' . ucfirst($mainSide)}()
                    / $image->{'get' . ucfirst($otherSide)}();
            $this->{$mainSide} = (int) $this->{$mainSide};
            $this->{$otherSide} = (int) ($this->{$mainSide} / $ratio);
        }
    }

}
