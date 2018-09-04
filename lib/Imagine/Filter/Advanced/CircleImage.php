<?php
/**
 * @author Simon Erhardt <simon.erhardt@liip.ch>
 * @license MIT (http://opensource.org/licenses/MIT)
 */

namespace Imagine\Filter\Advanced;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Filter\FilterInterface;
use Imagine\Gd\Image;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Gd\Imagine;
use Imagine\Image\Point\Center;
use Imagine\Image\Point;

/**
 * Class CircleImage
 * Let's circle an image
 *
 * @package Imagine\Filter\Advanced
 * @author Simon Erhardt <simon.erhardt@liip.ch>
 */
class CircleImage implements FilterInterface {
    private $radius = false;
    private $imagine;

    /**
     * Creates a round picture, set the radius and we will calculate
     * which pixels are inside the circle and copy them to a new image
     * with transparent background.
     * If you set the radius bigger then the half size of the
     * image it let you create round corners.
     *
     * @param int|boolean $radius Sets the radius, if false it will use half of the smaller side
     * uses half of size
     */
    public function __construct($radius = false)
    {
        if($radius && gettype($radius) == 'integer')
        {
            $this->radius = pow($radius,2);
        }
        $this->imagine = new Imagine();
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        if($this->radius == false)
        {
            if($image->getSize()->getHeight() >= $image->getSize()->getWidth()) {
                $this->radius = pow(floor($image->getSize()->getWidth() / 2),2);
            } else {
                $this->radius = pow(floor($image->getSize()->getHeight() / 2),2);
            }
        }

        $palette = $image->palette();
        if(!$palette->supportsAlpha())
        {
            $palette = new RGB();
            $image->usePalette($palette);
        }

        $transparent = $palette->color(array(255, 255, 255), 100);
        $newImage = $this->imagine->create(new Box($image->getSize()->getWidth(), $image->getSize()->getHeight()), $transparent);
        $newImage->usePalette($palette);

        $center = new Center($image->getSize());
        $circleX = $center->getX();
        $circleY = $center->getY();

        for($y = 0; $y < $image->getSize()->getHeight(); $y++)
        {
            for($x = 0; $x < $image->getSize()->getWidth(); $x++)
            {
                if((pow($x - $circleX, 2) + pow($y - $circleY, 2)) <= $this->radius)
                {
                    try
                    {
                        $point = new Point($x, $y);
                    }
                    catch(InvalidArgumentException $e)
                    {
                        continue;
                    }
                    $color = $image->getColorAt($point);
                    $newImage->draw()->dot($point,$color);
                }
            }
        }

        return $newImage;
    }
}