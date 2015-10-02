<?php

namespace Imagine\Gmagick;

class Balanced
{
    /**
     * get special offset for class
     *
     * @param  \Gmagick $original
     * @param  int      $targetWidth
     * @param  int      $targetHeight
     * @return array
     */
    public function getSpecialOffset(\Gmagick $original, $targetWidth, $targetHeight)
    {
        return $this->getRandomEdgeOffset($original, $targetWidth, $targetHeight);
    }
    /**
     *
     * @param  \Gmagick $original
     * @param  int      $targetWidth
     * @param  int      $targetHeight
     * @return array
     */
    protected function getRandomEdgeOffset(\Gmagick $original, $targetWidth, $targetHeight)
    {
        $measureImage = clone($original);
        // Enhance edges with radius 1
        $measureImage->edgeimage(1);
        // Turn image into a grayscale
        $measureImage->modulateImage(100, 0, 100);
        // Get the calculated offset for cropping
        return $this->getOffsetBalanced($measureImage, $targetWidth, $targetHeight);
    }

    /**
     * @param \Gmagick $originalImage
     * @param $targetWidth
     * @param $targetHeight
     * @return array
     * @throws \Exception
     */
    protected function getOffsetBalanced(\Gmagick $originalImage, $targetWidth, $targetHeight)
    {
        $size = array('width' => $originalImage->getImageWidth(), 'height' =>$originalImage->getImageHeight());
        var_dump($size);
        $points = array();
        $halfWidth = ceil($size['width']/2);
        $halfHeight = ceil($size['height']/2);
        // First quadrant
        $clone = clone($originalImage);
        $clone->cropImage($halfWidth, $halfHeight, 0, 0);
        $point = $this->getHighestEnergyPoint($clone);
        $points[] = array('x' => $point['x'], 'y' => $point['y'], 'sum' => $point['sum']);
        // Second quadrant
        $clone = clone($originalImage);
        $clone->cropImage($halfWidth, $halfHeight, $halfWidth, 0);
        $point = $this->getHighestEnergyPoint($clone);
        $points[] = array('x' => $point['x']+$halfWidth, 'y' => $point['y'], 'sum' => $point['sum']);
        // Third quadrant
        $clone = clone($originalImage);
        $clone->cropImage($halfWidth, $halfHeight, 0, $halfHeight);
        $point = $this->getHighestEnergyPoint($clone);
        $points[] = array('x' => $point['x'], 'y' => $point['y']+$halfHeight, 'sum' => $point['sum']);
        // Fourth quadrant
        $clone = clone($originalImage);
        $clone->cropImage($halfWidth, $halfHeight, $halfWidth, $halfHeight);
        $point = $point = $this->getHighestEnergyPoint($clone);
        $points[] = array('x' => $point['x']+$halfWidth, 'y' => $point['y']+$halfHeight, 'sum' => $point['sum']);
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
        if ($topleftX + $targetWidth > $size['width']) {
            $topleftX -= ($topleftX+$targetWidth) - $size['width'];
        }
        // If we don't have enough height for the crop, back up $topleftY until
        // we can make the image meet $targetHeight
        if ($topleftY+$targetHeight > $size['height']) {
            $topleftY -= ($topleftY+$targetHeight) - $size['height'];
        }

        $cropPoint = array('x'=>$topleftX, 'y'=>$topleftY);
        if ($cropPoint['x'] < 0) {
            $cropPoint['x'] = 0;
        } elseif ($cropPoint['y'] < 0) {
            $cropPoint['y'] = 0;
        }
        return $cropPoint;
    }

    /**
     * By doing random sampling from the image, find the most energetic point on the passed in
     * image
     *
     * @param \Gmagick $image
     * @return array
     * @throws \Exception
     */

    protected function getHighestEnergyPoint(\Gmagick $image)
    {
        $size = array('width' => $image->getImageWidth(), 'height' =>$image->getImageHeight());
        // It's more performant doing random pixel uplook via GD
        $im = imagecreatefromstring($image->getImageBlob());
        if ($im === false) {
            $msg = 'GD failed to create image from string';
            throw new \Exception($msg);
        }
        $xcenter = 0;
        $ycenter = 0;
        $sum = 0;
        // Only sample 1/50 of all the pixels in the image
        $sampleSize = round($size['height']*$size['width'])/50;
        for ($k=0; $k<$sampleSize; $k++) {
            $i = mt_rand(0, $size['width']-1);
            $j = mt_rand(0, $size['height']-1);
            $rgb = imagecolorat($im, $i, $j);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $val =  $this->rgb2bw($r, $g, $b);
            $sum += $val;
            $xcenter += ($i+1)*$val;
            $ycenter += ($j+1)*$val;
        }
        if ($sum) {
            $xcenter /= $sum;
            $ycenter /= $sum;
        }
        $point = array('x' => $xcenter, 'y' => $ycenter, 'sum' => $sum/round($size['height']*$size['width']));
        return $point;
    }

    /**
     * Returns a YUV weighted greyscale value
     *
     * @param  int $r
     * @param  int $g
     * @param  int $b
     * @return int
     * @see http://en.wikipedia.org/wiki/YUV
     */
    protected function rgb2bw($r, $g, $b)
    {
        return ($r*0.299)+($g*0.587)+($b*0.114);
    }
}