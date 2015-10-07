<?php

namespace Imagine\Gd;

use Imagine\Image\AbstractEntropy;

class Entropy extends AbstractEntropy
{
    /**
     * {@inheritdoc}
     */
    public function getSpecialOffset($original, $targetWidth, $targetHeight)
    {
        return $this->getEntropyOffsets($original, $targetWidth, $targetHeight);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntropyOffsets($original, $targetWidth, $targetHeight)
    {
        $measureImage = $this->cloneResource($original->getGdResource());
        // Enhance edges
        imagefilter($measureImage, IMG_FILTER_EDGEDETECT);
        // Turn image into a grayscale
        imagefilter($measureImage, IMG_FILTER_GRAYSCALE);
        imagefilter($measureImage, IMG_FILTER_EMBOSS);
        // Get the calculated offset for cropping
        return $this->getOffsetFromEntropy($measureImage, $targetWidth, $targetHeight);
    }

    /**
     * {@inheritdoc}
     */
    public function getOffsetFromEntropy($originalImage, $targetWidth, $targetHeight)
    {
        $originalWidth = imagesx($originalImage);
        $originalHeight = imagesy($originalImage);

        // The entropy works better on a blured image
        $image = $this->cloneResource($originalImage);
        imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
        $leftX = $this->slice($image, $originalWidth, $targetWidth, 'h');
        $topY = $this->slice($image, $originalHeight, $targetHeight, 'v');

        $cropPoint = array('x' => $leftX, 'y' => $topY);
        if ($cropPoint['x'] < 0) {
            $cropPoint['x'] = 0;
        } elseif ($cropPoint['y'] < 0) {
            $cropPoint['y'] = 0;
        }
        return $cropPoint;
    }

    /**
     * {@inheritdoc}
     */
    public function slice($image, $originalSize, $targetSize, $axis)
    {
        $aSlice = null;
        $bSlice = null;
        // Just an arbitrary size of slice size
        $sliceSize = ceil(($originalSize - $targetSize) / 25);
        $aBottom = $originalSize;
        $aTop = 0;
        // while there still are uninvestigated slices of the image
        while ($aBottom - $aTop > $targetSize) {
            // Make sure that we don't try to slice outside the picture
            $sliceSize = min($aBottom - $aTop - $targetSize, $sliceSize);
            // Make a top slice image
            if (!$aSlice) {
                $aSlice = $this->cloneResource($image);
                if ($axis === 'h') {
                    $rect = array('x' => $aTop , 'y' => 0, 'width' => $sliceSize, 'height' => $originalSize);
                    $aSlice = imagecrop($aSlice , $rect);
                } else {
                    $rect = array('x' => 0 , 'y' => $aTop, 'width' => $originalSize, 'height' => $sliceSize);
                    $aSlice = imagecrop($aSlice, $rect);
                }
            }
            // Make a bottom slice image
            if (!$bSlice) {
                $bSlice = $this->cloneResource($image);
                if ($axis === 'h') {
                    $rect = array('x' => $aBottom - $sliceSize , 'y' => 0, 'width' => $sliceSize, 'height' => $originalSize);
                    $bSlice = imagecrop($bSlice, $rect);
                } else {
                    $rect = array('x' => 0 , 'y' => $aBottom - $sliceSize, 'width' => $originalSize, 'height' => $sliceSize);
                    $bSlice = imagecrop($bSlice, $rect);
                }
            }

            // calculate slices potential
            $aPosition = ($axis === 'h' ? 'left' : 'top');
            $bPosition = ($axis === 'h' ? 'right' : 'bottom');
            $aPot = $this->getPotential($aPosition, $aTop, $sliceSize);
            $bPot = $this->getPotential($bPosition, $aBottom, $sliceSize);
            $canCutA = ($aPot <= 0);
            $canCutB = ($bPot <= 0);
            // if no slices are "cutable", we force if a slice has a lot of potential
            if (!$canCutA && !$canCutB) {
                if ($aPot * self::POTENTIAL_RATIO < $bPot) {
                    $canCutA = true;
                } elseif ($aPot > $bPot * self::POTENTIAL_RATIO) {
                    $canCutB = true;
                }
            }
            // if we can only cut on one side
            if ($canCutA xor $canCutB) {
                if ($canCutA) {
                    $aTop += $sliceSize;
                    $aSlice = null;
                } else {
                    $aBottom -= $sliceSize;
                    $bSlice = null;
                }
            } elseif ($this->grayscaleEntropy($aSlice) < $this->grayscaleEntropy($bSlice)) {
                // bSlice has more entropy, so remove aSlice and bump aTop down
                $aTop += $sliceSize;
                $aSlice = null;
            } else {
                $aBottom -= $sliceSize;
                $bSlice = null;
            }
        }
        return $aTop;
    }

    /**
     * {@inheritdoc}
     */
    public function getPotential($position, $top, $sliceSize)
    {
        $safeZoneList = array();
        $safeRatio = 0;
        if ($position == 'top' || $position == 'left') {
            $start = $top;
            $end = $top + $sliceSize;
        } else {
            $start = $top - $sliceSize;
            $end = $top;
        }
        for ($i = $start; $i < $end; $i++) {
            foreach ($safeZoneList as $safeZone) {
                if ($position == 'top' || $position == 'bottom') {
                    if ($safeZone['top'] <= $i && $safeZone['bottom'] >= $i) {
                        $safeRatio = max($safeRatio, ($safeZone['right'] - $safeZone['left']));
                    }
                } else {
                    if ($safeZone['left'] <= $i && $safeZone['right'] >= $i) {
                        $safeRatio = max($safeRatio, ($safeZone['bottom'] - $safeZone['top']));
                    }
                }
            }
        }
        return $safeRatio;
    }

    /**
     * {@inheritdoc}
     */
    public function grayscaleEntropy($image)
    {
        // The histogram consists of a list of 0-254 and the number of pixels that has that value
        $histogram = $this->ImageHistogram($image);
        return $this->getEntropy($histogram, $this->area($image));
    }

    /**
     * {@inheritdoc}
     */
    public function area($image)
    {
        return imagesx($image) * imagesy($image);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntropy($histogram, $area)
    {
        $value = 0.0;
        foreach ($histogram as $occur) {
            // calculates the percentage of pixels having this color value
            $p = $occur / $area;
            // A common way of representing entropy in scalar
            $value = $value + $p * log($p, 2);
        }
        // $value is always 0.0 or negative, so transform into positive scalar value
        return -$value;
    }

    /**
     * Return an histogram of color pixel
     *
     * @param resource $image
     * @return array
     */
    protected function ImageHistogram($image)
    {
        $colors = array();
        for ($i = 0; $i < imagesx($image); $i++) {
            for ($j = 0; $j < imagesy($image); $j++) {
                $color = imagecolorat($image, $i, $j);
                $colors[] = $color;
            }
        }
        $colorsOccur = array_count_values($colors);
        return $colorsOccur;
    }

    /**
     * @param resource $image
     * @return resource
     */
    protected function cloneResource($image) {
        $width = imagesx($image);
        $height = imagesy($image);

        $copy = imagecreatetruecolor($width, $height);

        imagecopy($copy, $image, 0, 0, 0, 0, $width, $height);

        return $copy;
    }
}
