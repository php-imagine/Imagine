<?php

namespace Imagine\Filter\Basic;

use Imagine\Filter\FilterInterface;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;

class CropBalanced implements FilterInterface
{
    /**
     * @var BoxInterface
     */
    private $size;

    /**
     * Constructs a CropBalanced filter
     *
     * @param BoxInterface   $size
     */
    public function __construct(BoxInterface $size)
    {
        $this->size = $size;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        return $image->resizeAndCropBalanced($this->size);
    }
}
