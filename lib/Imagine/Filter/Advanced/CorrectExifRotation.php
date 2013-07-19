<?php
namespace Imagine\Filter\Advanced;

use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Color;

/**
 * A filter to rotate the image according to its
 * given exif information
 */
class CorrectExifRotation implements FilterInterface
{
    /**
     * @var Color
     */
    private $color;

    /**
     * @var Array
     */
    private $exifData;

    /**
     * Requires an array of exifData to be handed over
     *
     * @param Array   $exifData
     * @param Color   $color
     */
    public function __construct(Array $exifData, Color $color = null)
    {
        $this->exifData = $exifData;
        $this->color = $color;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {

        if (isset($this->exifData['Orientation'])) {
            $orientation = (int) $this->exifData['Orientation'];

            $rotateVal = 0;
            switch($orientation) {
                case 8:
                    $rotateVal = -90;
		    break;
                case 3:
                    $rotateVal = 180;
                    break;
                case 6:
                    $rotateVal = 90;
                    break;
            }
            $image->rotate($rotateVal, $this->color);
        }

        return $image;
    }
}

