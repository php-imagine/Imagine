<?php

/**
 * CropBalanced
 *
 * This class calculates the most interesting point in the image by:
 *
 * 1. Dividing the image into four equally squares
 * 2. Find the most energetic point per square
 * 3. Finding the images weighted mean interest point
 */

namespace Imagine\Image;

Abstract class AbstractBalanced
{
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
     * Apply image filter and return the crop point
     *
     * @param  mixed    $original
     * @param  int      $targetWidth
     * @param  int      $targetHeight
     * @return array    The crop point coordinate
     */
    abstract function getRandomEdgeOffset($original, $targetWidth, $targetHeight);

    /**
     * Crop image in four to return four energetic points
     *
     * @param mixed     $originalImage
     * @param int       $targetWidth
     * @param int       $targetHeight
     * @return array    The crop point coordinate
     * @throws \Exception
     */
    abstract function getOffsetBalanced($originalImage, $targetWidth, $targetHeight);

    /**
     * By doing random sampling from the image, find the most energetic point on the passed in
     * image
     *
     * @param mixed     $image
     * @return array    The coordinate of the most energetic point
     * @throws \Exception
     */
    abstract function getHighestEnergyPoint($image);


    /**
     * Returns a YUV weighted greyscale value
     *
     * @param  int $r
     * @param  int $g
     * @param  int $b
     * @return int
     * @see http://en.wikipedia.org/wiki/YUV
     */
    protected function getLuminanceFromRGB($r, $g, $b)
    {
        return ($r * 0.299) + ($g * 0.587) + ($b * 0.114);
    }
}
