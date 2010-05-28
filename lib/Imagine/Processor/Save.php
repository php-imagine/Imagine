<?php

namespace Imagine\Processor;

use Imagine\Image;

class Save extends AbstractCommand {
    protected $path;

    public function __construct($path) {
        if ( ! is_dir($path)) {
            if ( ! @mkdir($path, 0777, true)) {
                throw new \InvalidArgumentException(
                    'Directory ' . $path . ' is not a valid directory'
                );
            }
        }
        $this->path = realpath($path);
    }

    protected function _process(Image $image) {
        $file = $this->getFileName($image);
        if (false === file_put_contents($file, $image->getContent())) {
            throw new \RuntimeException('Could not write file ' . $file);
        }
        $image->setPath($file);
    }

    protected function _restore(Image $image, array $snapshot) {
        $image->setPath($snapshot['path']);
//        $this->_process($image);
//        $file = $this->getFileName($image);
//        if (file_exists($file)) {
//            if (false === file_put_contents($file, $snapshot['content'])) {
//                throw new \RuntimeException('Could not revert file ' . $file);
//            }
//        } else {
//            if ( ! unlink($file)) {
//                throw new \RuntimeException('Could not remove file ' . $file);
//            }
//        }
//        var_dump($file);
    }

    private function getFileName(Image $image) {
        return $this->path . DIRECTORY_SEPARATOR . $image->getName() .
            $image->getExtension();
    }
}
