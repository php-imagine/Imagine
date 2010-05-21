<?php

namespace Imagine;

class ImageProcessor {

	protected $image;
	protected $commands = array();
	protected $keepRatio = false;

	public function  __construct(Image $image) {
		$this->image = $image;
	}

	public function getImage() {
		return $this->image;
	}

	public function setSize($width, $height) {
		$this->commands[] = new Processor\SetSizeCommand($width, $height, $this->keepRatio);
		return $this;
	}

	public function keepRatio($bool = null) {
		if (null === $bool) {
			return $this->keepRatio;
		}
		$this->keepRatio = (bool) $bool;
		return $this;
	}
	// @todo: this is heavy, need to find a more lightweight implementation
	public function process() {
		foreach ($this->commands as $command) {
			$command->process($this->image);
			$resource = $command->getProcessed();
			if (isset ($resource)) {
				ob_start();
				switch ($type = $this->image->getType()) {
					case \IMAGETYPE_GIF:
						imagegif($resource);
						break;
					case \IMAGETYPE_JPEG:
						imagejpeg($resource);
						break;
					case \IMAGETYPE_PNG:
						imagepng($resource);
						break;
				}
				$this->image->setContentType(image_type_to_mime_type($type));
				$this->image->setContent(ob_get_clean());
			}
		}
	}
}
