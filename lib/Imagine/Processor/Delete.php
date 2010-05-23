<?php

namespace Imagine\Processor;

use Imagine\Image;

class Delete extends AbstractCommand {

    protected function _process(Image $image) {
        if ( ! unlink($image->getPath())) {
            throw new \RuntimeException('Could not delete ' . $image->getPath());
        }
    }

    protected function _restore(Image $image, array $snapshot) {
        if ( ! file_put_contents($snapshot['path'], $snapshot['content'])) {
            throw new \RuntimeException('Could not restore ' . $snapshot['path']);
        }
    }
}
