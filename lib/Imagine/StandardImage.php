<?php

namespace Imagine;

class StandardImage implements Image {
    protected $name;
    protected $type;
    protected $contentType;
    protected $content;
    protected $height;
    protected $width;
    protected $top;
    protected $left;
    protected $path;
    protected $processedImage;

    public function setName($name) {
        $this->name = $name;
    }
    public function getName() {
        return $this->name;
    }
    public function getType() {
        return $this->type;
    }
    public function getContentType() {
        return $this->contentType;
    }
    public function getContent() {
        return $this->content;
    }
    public function setHeight($height) {}
    public function getHeight() {
        return $this->height;
    }
    public function setWidth($width) {}
    public function getWidth() {
        return $this->width;
    }
    
    public function getPath() {
        return $this->path;
    }
    public function resize($width, $height) {
        $srcImage = imagecreatefromstring($this->getContent());
        $destImage = imagecreatetruecolor($width, $height);
        if ( ! imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $width, $height, $this->width, $this->height)) {
            throw new RuntimeException('Could not resize the image');
        }
        $this->width = $width;
        $this->height = $height;
        $this->processedImage = $destImage;
        imagedestroy($srcImage);
    }
    
    public function crop($x, $y, $width, $height) {
        $srcImage = imagecreatefromstring($this->getContent());
        $destImage = imagecreatetruecolor($width, $height);
        if ( ! imagecopy($destImage, $srcImage, 0, 0, $x, $y, $width, $height)) {
            throw new RuntimeException('Could not resize the image');
        }
        $this->width = $width;
        $this->height = $height;
        $this->processedImage = $destImage;
        imagedestroy($srcImage);
    }
    
    public function getProcessedImage() {
        return $this->processedImage;
    }
}