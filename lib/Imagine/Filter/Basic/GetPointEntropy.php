<?php


use Imagine\Filter\FilterInterface;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;

class GetPointEntropy implements FilterInterface
{
    /**
     * @var mixed
     */
    private $image;
    /**
     * @var BoxInterface
     */
    private $size;

    /**
     * Constructs a GetPointEntropy filter
     *
     * @param $image
     * @param BoxInterface $size
     */
    public function __construct($image, BoxInterface $size)
    {
        $this->image = $image;
        $this->size = $size;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        return $image->getPointEntropy($this->image, $this->size);
    }
}