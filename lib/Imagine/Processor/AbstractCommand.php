<?php

namespace Imagine\Processor;

use Imagine\Image;

abstract class AbstractCommand implements Command {
    
    private $snapshots = array();
    private $imageResource;

    public function process(Image $image) {
        $this->takeSnapshot($image);
        $this->imageResource = $this->_process($image);
        if (null !== $this->imageResource) {
            ob_start();
            switch ($type = $image->getType()) {
                case \IMAGETYPE_GIF:
                    imagegif($this->imageResource);
                    break;
                case \IMAGETYPE_JPEG:
                    imagejpeg($this->imageResource);
                    break;
                case \IMAGETYPE_PNG:
                    imagepng($this->imageResource);
                    break;
                default:
                    throw new \InvalidArgumentException(
                        'Unsupported image type: ' . $type
                    );
            }
            $image->setContentType(image_type_to_mime_type($type));
            $image->setContent(ob_get_clean());
        }
    }

    public function restore(Image $image) {
        $this->_restore($image, $this->getSnapshot($image));
        $this->imageResource = null;
        $this->dropSnapshot($image);
    }

    public final function getImageResource() {
        return $this->imageResource;
    }

    private function takeSnapshot(Image $image) {
        $id = spl_object_hash($image);
        $this->snapshots[$id] = array(
            'content' => $image->getContent(),
            'content_type' => $image->getContentType(),
            'width' => $image->getWidth(),
            'height' => $image->getHeight(),
            'name' => $image->getName(),
            'path' => $image->getPath(),
            'type' => $image->getType(),
        );
    }

    private function dropSnapshot(Image $image) {
        $id = spl_object_hash($image);
        if (isset ($this->snapshots[$id])) {
            unset ($this->snapshots[$id]);
        }
    }

    private function getSnapshot(Image $image) {
        $id = spl_object_hash($image);
        return (isset ($this->snapshots[$id])) ?
            $this->snapshots[$id] : null;
    }

    protected abstract function _process(Image $image);
    protected abstract function _restore(Image $image, array $snapshot);

}