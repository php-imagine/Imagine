<?php

namespace Imagine;

use Imagine\Processor\Command;

class ImageProcessor {

    protected $commands = array();

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

	public function delete() {
		$command = new Processor\DeleteCommand();
        $this->addCommand($command);
        return $this;
	}
	
	public function save($dir) {
		$command = new Processor\SaveCommand($dir);
		$this->addCommand($command);
		return $this;
	}

    // @todo: this is heavy, need to find a more lightweight implementation
    public function process(Image $image) {
        foreach ($this->commands as $command) {
            $command->process($image);
		}
    }

    public function restore(Image $image) {
		foreach (array_reverse($this->commands) as $key => $command) {
			$command->restore($image);
			unset ($this->commands[$key]);
		}
    }

    public function addCommand(Command $command) {
        $this->commands[] = $command;
    }
}
