<?php

namespace Imagine\Image;

Abstract class AbstractEntropy
{
    const POTENTIAL_RATIO = 1.5;

    /**
     * Get special offset for class
     *
     * @param  mixed    $original
     * @param  int      $targetWidth
     * @param  int      $targetHeight
     * @return array    The crop point coordinate
     */
    abstract function getSpecialOffset($original, $targetWidth, $targetHeight);

    /**
     * Get the topleftX and topleftY that will can be passed to a cropping method.
     *
     * @param  mixed    $original
     * @param  int      $targetWidth
     * @param  int      $targetHeight
     * @return array    The crop point coordinate
     */
    abstract function getEntropyOffsets($original, $targetWidth, $targetHeight);

    /**
     * Get the offset of where the crop should start
     *
     * @param  mixed    $originalImage
     * @param  int      $targetWidth
     * @param  int      $targetHeight
     * @return array    The crop point coordinate
     */
    abstract function getOffsetFromEntropy($originalImage, $targetWidth, $targetHeight);

    /**
     * Slice Image to find the most entropic point for the crop method
     *
     * @param mixed     $image
     * @param mixed     $originalSize
     * @param mixed     $targetSize
     * @param mixed     $axis         h = horizontal, v = vertical
     * @access protected
     * @return int|mixed
     */
    abstract function slice($image, $originalSize, $targetSize, $axis);

    /**
     * @param mixed $position
     * @param mixed $top
     * @param mixed $sliceSize
     * @access protected
     * @return int|mixed
     */
    abstract function getPotential($position, $top, $sliceSize);

    /**
     * Calculate the entropy for this image.
     * A higher value of entropy means more noise / liveliness / color / business
     *
     * @param  mixed $image
     * @return float
     *
     * @see http://brainacle.com/calculating-image-entropy-with-python-how-and-why.html
     * @see http://www.mathworks.com/help/toolbox/images/ref/entropy.html
     */
    abstract function grayscaleEntropy($image);

    /**
     * Get the area in pixels for this image
     *
     * @param  mixed $image
     * @return int
     */
    abstract function area(\Imagick $image);

    /**
     * @param  array $histogram - a value[count] array
     * @param  int   $area
     * @return float
     */
    abstract function getEntropy($histogram, $area);
}