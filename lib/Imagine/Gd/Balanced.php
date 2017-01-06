<?php

namespace Imagine\Gd;

use Imagine\Image\AbstractBalanced;

class Balanced extends AbstractBalanced
{
    /**
     * {@inheritdoc}
     */
    public function getSpecialOffset($original, $targetWidth, $targetHeight)
    {
        return $this->getRandomEdgeOffset($original, $targetWidth, $targetHeight);
    }

    /**
     * {@inheritdoc}
     */
    public function getRandomEdgeOffset($original, $targetWidth, $targetHeight)
    {
        $measureImage = $this->cloneResource($original->getGdResource());
        // Enhance edges with radius 1
        imagefilter($measureImage, IMG_FILTER_EDGEDETECT);
        // Turn image into a grayscale
        imagefilter($measureImage, IMG_FILTER_GRAYSCALE);
        imagefilter($measureImage, IMG_FILTER_EMBOSS);
        // Get the calculated offset for cropping
        return $this->getOffsetBalanced($measureImage, $targetWidth, $targetHeight);
    }

    /**
     * {@inheritdoc}
     */
    public function getOffsetBalanced($originalImage, $targetWidth, $targetHeight)
    {
        $width = imagesx($originalImage);
        $height = imagesy($originalImage);
        $points = array();
        $halfWidth = ceil($width / 2);
        $halfHeight = ceil($height / 2);

        // First quadrant
        $clone = $this->cloneResource($originalImage);
        $rect = array('x' => 0 , 'y' => 0, 'width' => $halfWidth, 'height'=> $halfHeight);
        $clone1 = imagecrop($clone, $rect);
        $point = $this->getHighestEnergyPoint($clone1);
        $points[] = array('x' => $point['x'], 'y' => $point['y'], 'sum' => $point['sum']);
        // Second quadrant
        $clone = $this->cloneResource($originalImage);
        $rect = array('x' => $halfWidth , 'y' => 0, 'width' => $halfWidth, 'height'=> $halfHeight);
        $clone2 = imagecrop($clone, $rect);
        $point = $this->getHighestEnergyPoint($clone2);
        $points[] = array('x' => $point['x'] + $halfWidth, 'y' => $point['y'], 'sum' => $point['sum']);
        // Third quadrant
        $clone = $this->cloneResource($originalImage);
        $rect = array('x' => 0 , 'y' => $halfHeight, 'width' => $halfWidth, 'height'=> $halfHeight);
        $clone3 = imagecrop($clone, $rect);
        $point = $this->getHighestEnergyPoint($clone3);
        $points[] = array('x' => $point['x'], 'y' => $point['y'] + $halfHeight, 'sum' => $point['sum']);
        // Fourth quadrant
        $clone = $this->cloneResource($originalImage);
        $rect = array('x' => $halfWidth , 'y' => $halfHeight, 'width' => $halfWidth, 'height'=> $halfHeight);
        $clone4 = imagecrop($clone, $rect);
        $point = $this->getHighestEnergyPoint($clone4);
        $points[] = array('x' => $point['x'] + $halfWidth, 'y' => $point['y'] + $halfHeight, 'sum' => $point['sum']);

        // get the totalt sum value so we can find out a mean center point
        $totalWeight = array_reduce(
            $points,
            function ($result, $array) {
                return $result + $array['sum'];
            }
        );
        $centerX = 0;
        $centerY = 0;
        // Calulate the mean weighted center x and y
        $totalPoints = count($points);
        for ($idx=0; $idx < $totalPoints; $idx++) {
            $centerX += $points[$idx]['x'] * ($points[$idx]['sum'] / $totalWeight);
            $centerY += $points[$idx]['y'] * ($points[$idx]['sum'] / $totalWeight);
        }
        // From the weighted center point to the topleft corner of the crop would be
        $topleftX = max(0, ($centerX - $targetWidth / 2));
        $topleftY = max(0, ($centerY - $targetHeight / 2));
        // If we don't have enough width for the crop, back up $topleftX until
        // we can make the image meet $targetWidth
        if ($topleftX + $targetWidth > $width) {
            $topleftX -= ($topleftX + $targetWidth) - $width;
        }
        // If we don't have enough height for the crop, back up $topleftY until
        // we can make the image meet $targetHeight
        if ($topleftY + $targetHeight > $height) {
            $topleftY -= ($topleftY + $targetHeight) - $height;
        }

        $cropPoint = array('x' => $topleftX, 'y' => $topleftY);
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
    public function getHighestEnergyPoint($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $xcenter = 0;
        $ycenter = 0;
        $sum = 0;
        // Only sample 1/50 of all the pixels in the image
        $sampleSize = round($width * $height) / 50;
        for ($k = 0; $k < $sampleSize; $k++) {
            $i = mt_rand(0, $width - 1);
            $j = mt_rand(0, $height - 1);
            $rgb = imagecolorat($image, $i, $j);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $val =  $this->getLuminanceFromRGB($r, $g, $b);
            $sum += $val;
            $xcenter += ($i + 1) * $val;
            $ycenter += ($j + 1) * $val;
        }
        if ($sum) {
            $xcenter /= $sum;
            $ycenter /= $sum;
        }
        $point = array('x' => $xcenter, 'y' => $ycenter, 'sum' => $sum / round($width * $height));
        return $point;
    }

    /**
     * Clone and return an image
     *
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
