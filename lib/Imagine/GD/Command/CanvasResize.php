<?php

namespace Imagine\GD\Command;

use Imagine\GD\Utils;

/**
 * GD canvas resize command
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
class CanvasResize implements \Imagine\Command
{
    const CENTER = 1;

    /**
     * Resized image canvas width
     *
     * @var int
     */
    protected $width;

    /**
     * Resized image canvas height
     *
     * @var int
     */
    protected $height;

    /**
     * Placement mode
     *
     * @var int
     */
    protected $mode;

    /**
     * Canvas fill color
     *
     * @var array
     */
    protected $fillColor;

    /**
     * Constructs a canvas resize operation.
     *
     * The canvas fill color may be three (RGB) or four (RGBa) integer values,
     * which will be used as arguments to imagecolorallocate() or
     * imagecolorallocatealpha(), respectively.  Opaque white will be used if
     * no fill color is given.
     *
     * One of the following mode constants may optionally be specified:
     *
     *  - CENTER: Center the original image on the resized canvas
     *
     * @link http://php.net/manual/en/function.imagecolorallocate.php
     * @link http://php.net/manual/en/function.imagecolorallocatealpha.php
     * @param int   $width
     * @param int   $height
     * @param array $fillColor
     * @param int   $mode
     * @throws \InvalidArgumentException
     */
    public function __construct($width, $height, array $fillColor = null, $mode = self::CENTER)
    {
        $this->width = (int) $width;
        $this->height = (int) $height;
        $this->fillColor = $fillColor ?: array(255, 255, 255);
        $this->mode = $mode;

        if (! in_array(count($this->fillColor), array(3, 4))) {
            throw new \InvalidArgumentException('Invalid fillColor parameter: ' . $mode);
        }

        if (isset($mode) && !in_array($mode, array(self::CENTER))) {
            throw new \InvalidArgumentException('Invalid mode parameter: ' . $mode);
        }
    }

    /**
     * Process the canvas resize operation.
     *
     * @param \Imagine\Image $image
     * @throws \RuntimeException
     */
    public function process(\Imagine\Image $image)
    {
        $srcImage = $image->getResource();
        $dstImage = Utils::createResource($this->width, $this->height, $image->getType());

        $colorFunction = 'imagecolorallocate' . (count($this->fillColor) == 4 ? 'alpha' : '');
        $colorParams = array_merge(array($dstImage), $this->fillColor);

        if (false === ($color = call_user_func_array($colorFunction, $colorParams))) {
            throw new \RuntimeException('Could not allocate fill color');
        }

        // Use imagefilledrectangle() instead of imagefill() for alpha support
        if (! imagefilledrectangle($dstImage, 0, 0, $this->width, $this->height, $color)) {
            throw new \RuntimeException('Could not apply fill color');
        }

        // TODO: Add support for placements other than centered
        $srcCropWidth = min($image->getWidth(), $this->width);
        $srcCropHeight = min($image->getHeight(), $this->height);

        $dstPutX = intval(($this->width - $srcCropWidth) / 2);
        $dstPutY = intval(($this->height - $srcCropHeight) / 2);

        $srcGetX = intval(($image->getWidth() - $srcCropWidth) / 2);
        $srcGetY = intval(($image->getHeight() - $srcCropHeight) / 2);

        if (! imagecopy($dstImage, $srcImage, $dstPutX, $dstPutY, $srcGetX, $srcGetY, $srcCropWidth, $srcCropHeight)) {
            throw new \RuntimeException('Could not resize the image canvas');
        }

        $image->setResource($dstImage);
    }
}
