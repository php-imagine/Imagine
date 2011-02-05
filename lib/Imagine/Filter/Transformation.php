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

final class Transformation implements FilterInterface, ImageInterface
{
    private $filters = array();

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::getHeight()
     */
    public function getHeight()
    {

    }

    /**
     * (non-PHPdoc)
     * @see Imagine.ImageInterface::getWidth()
     */
    public function getWidth()
    {

    }

    /**
     * Applies a given FilterInterface onto given ImageInterface and returns
     * modified ImageInterface
     *
     * @param FilterInterface $filter
     * @param ImageInterface  $image
     *
     * @return ImageInterface
     */
    public function applyFilter(FilterInterface $filter, ImageInterface $image)
    {
        return $filter->apply($image);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return array_reduce($this->filters, array($this, 'applyFilter'), $image);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::copy()
     */
    public function copy()
    {
        return $this->add(new Copy());
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::crop()
     */
    public function crop($x, $y, $width, $height)
    {
        return $this->add(new Crop($x, $y, $width, $height));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::flipHorizontally()
     */
    public function flipHorizontally()
    {
        return $this->add(new FlipHorizontally());
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::flipVertically()
     */
    public function flipVertically()
    {
        return $this->add(new FlipVertically());
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::paste()
     */
    public function paste(ImageInterface $image, $x, $y)
    {
        return $this->add(new Paste($image, $x, $y));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::resize()
     */
    public function resize($width, $height)
    {
        return $this->add(new Resize($width, $height));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::apply()
     */
    public function rotate($angle, Color $background = null)
    {
        return $this->add(new Rotate($angle, $background));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::save()
     */
    public function save($path, array $options = array())
    {
        return $this->add(new Save($path, $options));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::show()
     */
    public function show($format, array $options = array())
    {
        return $this->add(new Show($format, $options));
    }

    /**
     * Registers a given FilterInterface in an internal array of filters for
     * later application to an instance of ImageInterface
     *
     * @param FilterInterface $filter
     * @return Transformation
     */
    public function add(FilterInterface $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }
}
