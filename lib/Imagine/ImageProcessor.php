<?php

namespace Imagine;

use Imagine\Processor\Command;

class ImageProcessor {

    protected $image;
    protected $commands = array();

    public function  __construct(Image $image = null) {
        $this->image = $image;
    }

    public function getImage() {
        return $this->image;
    }

    public function resize($width, $height) {
        $command = new Processor\ResizeCommand($width, $height);
        $this->addCommand($command);
        return $this;
    }

    public function crop($x, $y, $width, $height) {
        $command = new Processor\CropCommand($x, $y, $width, $height);
        $this->addCommand($command);
        return $this;
    }

    // @todo: this is heavy, need to find a more lightweight implementation
    public function process(Image $image = null) {
        if (null === $image) {
            $image = $this->image;
        }
        foreach ($this->commands as $command) {
            $command->process($image);
            $resource = $command->getImageResource();
            if (isset ($resource)) {
                ob_start();
                switch ($type = $image->getType()) {
                    case \IMAGETYPE_GIF:
                        imagegif($resource);
                        break;
                    case \IMAGETYPE_JPEG:
                        imagejpeg($resource);
                        break;
                    case \IMAGETYPE_PNG:
                        imagepng($resource);
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
    }

    public function restore(Image $image) {

    }

    public function addCommand(Command $command) {
        $this->commands[] = $command;
    }
}
