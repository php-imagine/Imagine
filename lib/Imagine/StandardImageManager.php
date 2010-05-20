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
        $destImage = $image->getProcessedImage();
        $file = $dir . DIRECTORY_SEPARATOR . $image->getName() . 
                image_type_to_extension($image->getType());
        $result = false;
        if (null !== ($destImage = $image->getProcessedImage())) {
            switch ($image->getType()) {
                case \IMAGETYPE_GIF:
                    $result = imagegif($destImage, $file);
                    break;
                case \IMAGETYPE_JPEG:
                    $result = imagejpeg($destImage, $file);
                    break;
                case \IMAGETYPE_PNG:
                    $result = imagepng($destImage, $file);
                    break;
                default:
                    throw new InvalidArgumentException(
                        'The only value image types are jpg, gif and png ' .
                        $image->getType() . ' given'
                    );
            }
            $this->setProperty($image, 'processedImage', null);
            $this->setProperty($image, 'content', file_get_contents($file));
            imagedestroy($destImage);
            if ( ! $result) {
                throw new RuntimeException('Could not save image ' . $image->getName());
            }
        } else {
            file_put_contents($file, $image->getContent());
        }
    }
    
    public function delete(Image $image) {
        if ( ! unlink($image->getPath())) {
            throw new RuntimeException('Could not delete image ' . $image->getName());
        }
    }
    
    public function fetchImage($path) {
        $pathInfo = pathinfo($path);
        $image = new StandardImage();
        if (false === ($size = getimagesize($path))) {
            throw new \InvalidArgumentException('Could not determine image info');
        }
        $this->setProperty($image, 'width', $size[0]);
        $this->setProperty($image, 'height', $size[1]);
        $this->setProperty($image, 'type', $size[2]);
        $this->setProperty($image, 'contentType', $size['mime']);
        $this->setProperty($image, 'path', realpath($path));
        $this->setProperty($image, 'name', $pathInfo['filename']);
        $this->setProperty($image, 'content', file_get_contents($path));
        $this->setProperty($image, 'top', 0);
        $this->setProperty($image, 'left', 0);
        return $image;
    }
    
    private function setProperty(StandardImage $image, $name, $value) {
        $class = $this->getReflectionClass($image);
        $property = $class->getProperty($name);
        if ($property->isPrivate() || $property->isProtected()) {
            $property->setAccessible(true);
        }
        $property->setValue($image, $value);
        if ($property->isPrivate() || $property->isProtected()) {
            $property->setAccessible(false);
        }
    }
    
    private function getReflectionClass(StandardImage $image) {
        static $classes;
        if ( ! isset ($classes)) {
            $classes = array();
        }
        $className = get_class($image);
        if ( ! isset ($classes[$className])) {
            $classes[$className] = new \ReflectionObject($image);
        }
        return $classes[$className];
    }
}