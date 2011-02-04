<?php

namespace Imagine\Filter\Basic;

use Imagine\ImageInterface;

use Imagine\Filter\FilterInterface;

class Save implements FilterInterface
{
    private $path;
    private $options;

    public function __construct($path, array $options = array())
    {
        $this->path    = $path;
        $this->options = $options;
    }

    public function apply(ImageInterface $image)
    {
        return $image->save($this->path, $this->options);
    }
}
