<?php
namespace Imagine\Filter\Advanced;

use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\ColorInterface;

/**
 * A filter to rotate the image according to its
 * given exif information
 *
 * @example
 * $imagine = new \Imagine\Imagick\Imagine();
 * $image = $imagine->open($fullPath);
 *
 * $exifData = exif_read_data($fullPath);
 * $filter = new CorrectExifRotation($exifData);
 * $image = $filter->apply($image);
 */
class CorrectExifRotation implements FilterInterface
{
    /**
     * @var Color
     */
    private $color = null;

    /**
     * @var Array
     */
    private $exifData = array();

    /**
     * Requires an array of exifData to be handed over
     *
     * @param Array   $exifData
     * @param Color   $color
     */
    public function __construct(Array $exifData, ColorInterface $color = null)
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
