<?php

namespace Imagine\GD\Command;

use Imagine\GD\Utils;

/**
 * GD crop command
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
class Crop implements \Imagine\Command
{
    /**
     * Source image x-coordinate for cropping origin
     *
     * @var int
     */
    protected $x;

    /**
     * Source image y-coordinate for cropping origin
     *
     * @var int
     */
    protected $y;

    /**
     * Crop area width
     *
     * @var int
     */
    protected $width;

    /**
     * Crop area height
     *
     * @var int
     */
    protected $height;

    /**
     * Constructs a crop operation.
     *
     * @param int $x      Source image x-coordinate for cropping origin
     * @param int $y      Source image y-coordinate for cropping origin
     * @param int $width  Crop area width
     * @param int $height Crop area height
     */
    public function __construct($x, $y, $width, $height)
    {
        $this->x = (int) $x;
        $this->y = (int) $y;
        $this->width = (int) $width;
        $this->height = (int) $height;
    }

    /**
     * Process the crop operation.
     *
     * @param \Imagine\Image $image
     * @throws \RuntimeException
     */
    public function process(\Imagine\Image $image)
    {
        if (($this->x + $this->width > $image->getWidth()) || ($this->y + $this->height > $image->getHeight())) {
            throw new \RuntimeException('Cropping parameters are out of bounds');
        }

        $srcImage = $image->getResource();
        $dstImage = Utils::createResource($this->width, $this->height, $image->getType());

        if (! imagecopy($dstImage, $srcImage, 0, 0, $this->x, $this->y, $this->width, $this->height)) {
            throw new \RuntimeException('Could not crop the image');
        }
        $image->setResource($dstImage);
    }
}
