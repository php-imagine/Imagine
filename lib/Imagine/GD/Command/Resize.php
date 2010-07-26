<?php

namespace Imagine\GD\Command;

/**
 * GD resize command
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
class Resize implements \Imagine\Command
{
    /**
     * Resized image width
     *
     * @var int
     */
    protected $width;

    /**
     * Resized image height
     *
     * @var int
     */
    protected $height;

    /**
     * Constructs a resize operation.
     *
     * If boolean true is given for either the width or height parameter, its
     * value will be calculated from the other dimension, which must be an
     * integer, and the aspect ratio of the source image.
     *
     * @param bool|int $width  Resize width; if true, infer from height
     * @param bool|int $height Resize height, if true, infer from height
     * @throws \InvalidArgumentException
     */
    public function __construct($width, $height)
    {
        $this->width = (true === $width) ? true : (int) $width;
        $this->height = (true === $height) ? true : (int) $height;

        if (true === $this->width && true === $this->height) {
            throw new \InvalidArgumentException('Width and height cannot both be booleans');
        }
    }

    /**
     * Process the resize operation.
     *
     * @param \Imagine\Image $image
     * @throws \RuntimeException
     */
    public function process(\Imagine\Image $image)
    {
        $this->adjustSize($image);

        $srcImage = $image->getResource();
        $destImage = imagecreatetruecolor($this->width, $this->height);
        if (! imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $this->width, $this->height, $image->getWidth(), $image->getHeight())) {
            throw new \RuntimeException('Could not resize the image');
        }
        $image->setResource($destImage);
    }

    /**
     * If either width or height is boolean true, calculate its integer value
     * from the other dimension and the image's aspect ratio.
     *
     * @param \Imagine\Image $image
     */
    private function adjustSize(\Imagine\Image $image)
    {
        // TODO: looks kinda ugly, need to find a better way to calculate side ratios
        $mainSide = false;
        if (true === $this->width) {
            $mainSide = 'height';
        } elseif (true === $this->height) {
            $mainSide = 'width';
        }
        if (false !== $mainSide) {
            $otherSide = ($mainSide === 'width') ? 'height' : 'width';
            $ratio = $image->{'get' . ucfirst($mainSide)}()
            / $image->{'get' . ucfirst($otherSide)}();
            $this->{$mainSide} = (int) $this->{$mainSide};
            $this->{$otherSide} = (int) ($this->{$mainSide} / $ratio);
        }
    }
}
