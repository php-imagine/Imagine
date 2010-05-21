<?php

namespace Imagine\Processor;

use Imagine\Image;

class ResizeCommand extends AbstractCommand {

    protected $width;
    protected $height;
    protected $resource;

    public function __construct($width, $height) {
        $this->width = (true === $width) ? true : (int) $width;
        $this->height = (true === $height) ? true : (int) $height;
    }

    protected function _process(Image $image) {
        $srcImage = $image->getResource();
        $this->adjustSize($image);
        $destImage = imagecreatetruecolor($this->width, $this->height);
        if ( ! imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $this->width, $this->height, $image->getWidth(), $image->getHeight())) {
            throw new \RuntimeException('Could not resize the image');
        }
        imagedestroy($srcImage);
        $image->setWidth($this->width);
        $image->setHeight($this->height);
        return $destImage;
    }

    protected function _restore(Image $image, array $snapshot) {
        $image->setContent($snapshot['content']);
        $image->setWidth($snapshot['width']);
        $image->setHeight($snapshot['height']);
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
