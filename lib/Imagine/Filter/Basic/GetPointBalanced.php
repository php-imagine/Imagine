<?php

namespace Imagine\Filter\Basic;

use Imagine\Filter\FilterInterface;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;

class GetPointBalanced implements FilterInterface
{
    /**
     * @var BoxInterface
     */
    private $size;

    /**
     * Constructs a GetPointEntropy filter
     *
     * @param BoxInterface $size
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
        return $image->getPointBalanced($this->size);
    }
}
