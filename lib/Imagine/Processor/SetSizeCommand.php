<?php

namespace Imagine\Processor;

use Imagine\Image;

class SetSizeCommand implements ProcessCommand {

	const
		CROP = 1,
		RESIZE = 2,
		RESIZE_WITH_RATIO = 3;

	protected $width;
	protected $height;
	protected $keepRatio;
	protected $crop;
	protected $resource;

	public function __construct($width, $height, $keepRatio = false) {
		$this->width = $width;
		$this->height = $height;
		$this->keepRatio = $keepRatio;
	}

	public function process(Image $image) {
        $srcImage = $image->getResource();
		$this->adjustSize($image);
        $destImage = imagecreatetruecolor($this->width, $this->height);
        if ( ! imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $this->width, $this->height, $image->getWidth(), $image->getHeight())) {
            throw new \RuntimeException('Could not resize the image');
        }
        imagedestroy($srcImage);
		$image->setSize($this->width, $this->height);
		$this->resource = $destImage;
	}

	public function getProcessed() {
		return $this->resource;
	}

	private function adjustSize(Image $image) {
		if ( ! $this->keepRatio) {
			return ;
		}
		$maxSide = ($image->getWidth() >= $image->getHeight()) ? 'width' : 'height';
		$otherSide = ($maxSide === 'width') ? 'height' : 'width';
		$ratio = $image->{'get' . ucfirst($maxSide)}()
			/ $image->{'get' . ucfirst($otherSide)}();
		$this->{$otherSide} = $this->{$maxSide} / $ratio;
	}

}
