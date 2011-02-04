<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter;

use Imagine\ImageInterface;
use Imagine\ImageFactoryInterface;
use Imagine\Filter\Basic\Copy;
use Imagine\Filter\Basic\Crop;
use Imagine\Filter\Basic\FlipVertically;
use Imagine\Filter\Basic\FlipHorizontally;
use Imagine\Filter\Basic\Paste;
use Imagine\Filter\Basic\Resize;
use Imagine\Filter\Basic\Rotate;
use Imagine\Filter\Basic\Save;
use Imagine\Filter\Basic\Show;

class CompositeFilter implements FilterInterface, ImageInterface
{
    private $filters = array();

    public function applyFilter(FilterInterface $filter, ImageInterface $image)
    {
        return $filter->apply($image);
    }

    public function apply(ImageInterface $image)
    {
        return array_reduce($this->filters, array($this, 'applyFilter'), $image);
    }

    public function copy()
    {
        $this->add(new Copy());
    }

    public function crop($x, $y, $width, $height)
    {
        $this->add(new Crop($x, $y, $width, $height));
    }

    public function flipHorizontally()
    {
        $this->add(new FlipHorizontally());
    }

    public function flipVertically()
    {
        $this->add(new FlipVertically());
    }

    public function paste(ImageInterface $image, $x, $y)
    {
        $this->add(new Paste($image, $x, $y));
    }

    public function resize($width, $height)
    {
        $this->add(new Resize($width, $height));
    }

    public function rotate($angle, Color $background = null)
    {
        $this->add(new Rotate($angle, $background));
    }

    public function save($path, array $options = array())
    {
        $this->add(new Save($path, $options));
    }

    public function show($format, array $options = array())
    {
        $this->add(new Show($format, $options));
    }

    public function add(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }
}
