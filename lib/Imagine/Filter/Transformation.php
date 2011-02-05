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

use Imagine\Filter\Basic\Thumbnail;

use Imagine\Color;

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

final class Transformation implements FilterInterface
{
    private $filters = array();

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
     * Stacks a copy transformation into the current transformations queue
     *
     * @return Transformation
     */
    public function copy()
    {
        return $this->add(new Copy());
    }

    /**
     * Stacks a crop transformation into the current transformations queue
     *
     * @return Transformation
     */
    public function crop($x, $y, $width, $height)
    {
        return $this->add(new Crop($x, $y, $width, $height));
    }

    /**
     * Stacks a horizontal flip transformation into the current transformations
     * queue
     *
     * @return Transformation
     */
    public function flipHorizontally()
    {
        return $this->add(new FlipHorizontally());
    }

    /**
     * Stacks a vertical flip transformation into the current transformations
     * queue
     *
     * @return Transformation
     */
    public function flipVertically()
    {
        return $this->add(new FlipVertically());
    }

    /**
     * Stacks a paste transformation into the current transformations queue
     *
     * @param ImageInterface $image
     * @param integer        $x
     * @param integer        $y
     *
     * @return Transformation
     */
    public function paste(ImageInterface $image, $x, $y)
    {
        return $this->add(new Paste($image, $x, $y));
    }

    /**
     * Stacks a resize transformation into the current transformations queue
     *
     * @param integer width
     * @param integer height
     *
     * @return Transformation
     */
    public function resize($width, $height)
    {
        return $this->add(new Resize($width, $height));
    }

    /**
     * Stacks a rotane transformation into the current transformations queue
     *
     * @param integer $angle
     * @param Color   $background
     *
     * @return Transformation
     */
    public function rotate($angle, Color $background = null)
    {
        return $this->add(new Rotate($angle, $background));
    }

    /**
     * Stacks a save transformation into the current transformations queue
     *
     * @param string $path
     * @param array  $options
     *
     * @return Transformation
     */
    public function save($path, array $options = array())
    {
        return $this->add(new Save($path, $options));
    }

    /**
     * Stacks a show transformation into the current transformations queue
     *
     * @param string $path
     * @param array  $options
     *
     * @return Transformation
     */
    public function show($format, array $options = array())
    {
        return $this->add(new Show($format, $options));
    }

    /**
     * Stacks a thumbnail transformation into the current transformation queue
     *
     * @param integer $width
     * @param integer $height
     * @param string  $mode
     * @param Color   $background
     */
    public function thumbnail($width, $height, $mode = ImageInterface::THUMBNAIL_INSET, Color $background = null)
    {
        return $this->add(new Thumbnail($width, $height, $mode, $background));
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
