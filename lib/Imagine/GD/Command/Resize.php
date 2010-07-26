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
        if ($this->mode) {
            $this->adjustSize($image);
        }

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
        switch ($this->mode) {
            case self::INFER_HEIGHT:
                $this->height = intval($this->width * $image->getHeight() / $image->getWidth());
                break;

            case self::INFER_WIDTH:
                $this->width = intval($this->height * $image->getWidth() / $image->getHeight());
                break;

            case self::AR_AROUND:
                list($this->width, $this->height) = $this->calcBox($image->getWidth(), $image->getHeight(), $this->width, $this->height, true);
                break;

            case self::AR_WITHIN:
                list($this->width, $this->height) = $this->calcBox($image->getWidth(), $image->getHeight(), $this->width, $this->height);
                break;
        }
    }

    /**
     * Perform box calculations on "subject" dimensions with respect to "pov".
     *
     * @param integer $subjectX   Subject (x dimension)
     * @param integer $subjectY   Subject (y dimension)
     * @param integer $povX       POV (x dimension)
     * @param integer $povY       POV (y dimension)
     * @param boolean $atLeast    At least "subject" dimensions?
     * @param boolean $preserveAR Preserve aspect ratio?
     * @param boolean $scaleUp    Scale "subject" up?
     * @return array (x, y)
     */
    protected function calcBox($subjectX, $subjectY, $povX, $povY, $atLeast = false, $preserveAR = true, $scaleUp = true) {
        if (!($subjectX > 0 && $subjectY > 0 && $povX > 0 && $povY > 0)) {
            throw new \InvalidArgumentException('Dimensions must be positive integers');
        }

        // Initialize return values, in case resizing doesn't take place
        $calcX = $subjectX;
        $calcY = $subjectY;

        // Only resize "from" if "pov" is smaller, or explicitly scaling up
        if ($subjectX >= $povX || $subjectY >= $povX || $scaleUp) {
            // Calculate scale ratios ("pov":"from") for each dimension
            $xRatio = $povX / $subjectX;
            $yRatio = $povY / $subjectY;

            // Calculate the new size based on the chosen ratio
            if ($preserveAR) {
                $ratio = $atLeast ? max($xRatio, $yRatio) : min($xRatio, $yRatio);
                $calcX = intval($subjectX * $ratio);
                $calcY = intval($subjectY * $ratio);
            } else {
                $calcX = intval($subjectX * $xRatio);
                $calcY = intval($subjectY * $yRatio);
            }
        }

        return array($calcX, $calcY);
    }
}
