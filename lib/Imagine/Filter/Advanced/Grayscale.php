<?php

namespace Imagine\Filter\Advanced;

use \Imagine\Filter\FilterInterface;
use \Imagine\Image\Color;
use \Imagine\Image\ImageInterface;
use \Imagine\Image\Point;

class Grayscale implements FilterInterface
{
    /**
     * @var Imagine\Image\PointInterface
     */
    private $placement;

    /**
     * Applies scheduled transformation to ImageInterface instance
     * Returns processed ImageInterface instance
     *
     * @param \Imagine\Image\ImageInterface $image
     *
     * @return \Imagine\Image\ImageInterface
     */
    public function apply(ImageInterface $image)
    {
        for ($y = 0; $y < $image->getSize()->getHeight(); $y++)
            for ($x = 0; $x < $image->getSize()->getWidth(); $x++)
            {
                $color = $image->getColorAt(new Point($x, $y));
                $gray  = round(($color->getRed() + $color->getBlue() + $color->getGreen())/3);
                $image->draw()->dot(new Point($x, $y), new Color(array(
                    'red'   => $gray,
                    'green' => $gray,
                    'blue'  => $gray
                )));
            }

        return $image;
    }
}
