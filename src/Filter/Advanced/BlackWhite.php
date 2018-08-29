<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter\Advanced;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

/**
 * This filter calculates, for each pixel of an image, whether it is ligher or darker than a threshold.
 * If the pixel is lighter than the thresold it will be black, otherwise it will be light.
 * The result is an image with only black and white pixels (black pixels for ligher colors, white pixels for darker colors).
 */
class BlackWhite extends OnPixelBased implements FilterInterface
{
    /**
     * @var \Imagine\Filter\Advanced\Grayscale
     */
    protected $grayScaleFilter;

    /**
     * Initialize this filter.
     *
     * @param int $threshold the dask/light threshold, from 0 (all black) to 255 (all white)
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    public function __construct($threshold)
    {
        if (!(0 <= $threshold && $threshold <= 255)) {
            throw new InvalidArgumentException('$threshold has to be between 0 and 255');
        }

        $this->grayScaleFilter = new Grayscale();

        $rgb = new RGB();
        parent::__construct(
            function (ImageInterface $image, Point $point) use ($threshold, $rgb) {
                $newRedValue = $image->getColorAt($point)->getValue(ColorInterface::COLOR_RED) < $threshold ? 255 : 0;
                $image->draw()->dot($point, $rgb->color(array($newRedValue, $newRedValue, $newRedValue)));
            }
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Filter\Advanced\OnPixelBased::apply()
     */
    public function apply(ImageInterface $image)
    {
        $grayScaledImage = $this->grayScaleFilter->apply($image);

        return parent::apply($grayScaledImage);
    }
}
