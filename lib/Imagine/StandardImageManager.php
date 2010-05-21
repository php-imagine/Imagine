<?php

namespace Imagine;

class StandardImageManager implements ImageManager {

    protected $images = array();

    public function save(Image $image) {
        $pathInfo = pathinfo($image->getPath());
        $dir = $pathInfo['dirname'];
        if (!is_dir($dir)) {
            if ( ! @mkdir($dir, 0777, true)) {
                throw new RuntimeException('Could not create directory ' . $dir);
            }
        }
        $file = $dir . DIRECTORY_SEPARATOR . $image->getName() . 
                image_type_to_extension($image->getType());
        file_put_contents($file, $image->getContent());
    }
    
    public function delete(Image $image) {
        if ( ! unlink($image->getPath())) {
            throw new RuntimeException('Could not delete image ' . $image->getName());
        }
    }
    
    public function fetchImage($path) {
        return new StandardImage($path);
    }
}