<?php

namespace Imagine\Processor;

use Imagine\Image;

interface ProcessCommand {
    public function process(Image $image);
	public function getProcessed();
}
