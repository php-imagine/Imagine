<?php

namespace Imagine\GD\Command;

use Imagine\GD\Utils;

/**
 * GD resize command
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
class Resize implements \Imagine\Command
{
    const INFER_HEIGHT = 1;
    const INFER_WIDTH = 2;
    const AR_AROUND = 3;
    const AR_WITHIN = 4;

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
     * Resize mode
     *
     * @var int
     */
    protected $mode;

    /**
     * Constructs a resize operation.
     *
     * One of the following mode constants may optionally be specified:
     *
     *  - INFER_HEIGHT: Calculate the resize height based on the given width and
     *                  aspect ratio of the processed image.
     *  - INFER_WIDTH:  Calculate the resize width based on the given height and
     *                  aspect ratio of the processed image.
     *  - AR_AROUND:    Resize the image to the smallest size around the given
     *                  width and height, while still preserving aspect ratio.
     *  - AR_WITHIN:    Resize the image to the largest size within the given
     *                  width and height, while still preserving aspect ratio.
     *
     * @param int $width
     * @param int $height
     * @param int $mode
     * @throws \InvalidArgumentException
     */
    public function __construct($width, $height, $mode = null)
    {
        $this->width = (int) $width;
        $this->height = (int) $height;
        $this->mode = $mode;

        if (isset($mode) && !in_array($mode, array(self::INFER_HEIGHT, self::INFER_WIDTH, self::AR_AROUND, self::AR_WITHIN))) {
            throw new \InvalidArgumentException('Invalid mode parameter: ' . $mode);
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
        list($width, $height) = $this->adjustSize($image);

        $srcImage = $image->getResource();
        $dstImage = Utils::createResource($width, $height, $image->getType());

        if (! imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $width, $height, $image->getWidth(), $image->getHeight())) {
            throw new \RuntimeException('Could not resize the image');
        }
        $image->setResource($dstImage);
    }

    /**
     * If either width or height is boolean true, calculate its integer value
     * from the other dimension and the image's aspect ratio.
     *
     * @param \Imagine\Image $image
     * @return array width, height
     */
    private function adjustSize(\Imagine\Image $image)
    {
        switch ($this->mode) {
            case self::INFER_HEIGHT:
                $width  = $this->width;
                $height = intval($this->width * $image->getHeight() / $image->getWidth());
                break;

            case self::INFER_WIDTH:
                $width  = intval($this->height * $image->getWidth() / $image->getHeight());
                $height = $this->height;
                break;

            case self::AR_AROUND:
                list($width, $height) = Utils::getBoxForAspectRatio($image->getWidth() / $image->getHeight(), $this->width, $this->height, true);
                break;

            case self::AR_WITHIN:
                list($width, $height) = Utils::getBoxForAspectRatio($image->getWidth() / $image->getHeight(), $this->width, $this->height, false);
                break;

            default:
                $width  = $this->width;
                $height = $this->height;
                break;
        }

        return array($width, $height);
    }
}
