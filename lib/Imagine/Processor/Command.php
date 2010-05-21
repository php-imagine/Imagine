<?php

namespace Imagine\Processor;

use Imagine\Image;

interface Command {
    public function process(Image $image);
    public function restore(Image $image);
    public function getImageResource();
}
