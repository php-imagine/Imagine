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

use Imagine\Filter\Basic\Copy;
use Imagine\Filter\Basic\Crop;
use Imagine\Filter\Basic\FlipVertically;
use Imagine\Filter\Basic\FlipHorizontally;
use Imagine\Filter\Basic\Paste;
use Imagine\Filter\Basic\Resize;
use Imagine\Filter\Basic\Rotate;
use Imagine\Filter\Basic\Save;
use Imagine\Filter\Basic\Show;
use Imagine\Filter\Basic\Thumbnail;
use Imagine\ImageInterface;
use Imagine\ImageFactoryInterface;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

final class Transformation implements FilterInterface
{
    /**
     * @var array
     */
    private $filters = array();

    /**
     * Applies a given FilterInterface onto given ImageInterface and returns
     * modified ImageInterface
     *
     * @param Imagine\Filter\FilterInterface $filter
     * @param Imagine\ImageInterface         $image
     *
     * @return Imagine\ImageInterface
     */
    public function applyFilter(ImageInterface $image, FilterInterface $filter)
    {
        return $filter->apply($image);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return array_reduce(
            $this->filters,
            array($this, 'applyFilter'),
            $image
        );
    }

    /**
     * Stacks a copy transformation into the current transformations queue
     *
     * @return Imagine\Filter\Transformation
     */
    public function copy()
    {
        return $this->add(new Copy());
    }

    /**
     * Stacks a crop transformation into the current transformations queue
     *
     * @param Imagine\Image\PointInterface $start
     * @param Imagine\Image\BoxInterface   $size
     *
     * @return Imagine\Filter\Transformation
     */
    public function crop(PointInterface $start, BoxInterface $size)
    {
        return $this->add(new Crop($start, $size));
    }

    /**
     * Stacks a horizontal flip transformation into the current transformations
     * queue
     *
     * @return Imagine\Filter\Transformation
     */
    public function flipHorizontally()
    {
        return $this->add(new FlipHorizontally());
    }

    /**
     * Stacks a vertical flip transformation into the current transformations
     * queue
     *
     * @return Imagine\Filter\Transformation
     */
    public function flipVertically()
    {
        return $this->add(new FlipVertically());
    }

    /**
     * Stacks a paste transformation into the current transformations queue
     *
     * @param Imagine\ImageInterface       $image
     * @param Imagine\Image\PointInterface $start
     *
     * @return Imagine\Filter\Transformation
     */
    public function paste(ImageInterface $image, Point $start)
    {
        return $this->add(new Paste($image, $start));
    }

    /**
     * Stacks a resize transformation into the current transformations queue
     *
     * @param Imagine\Image\BoxInterface
     *
     * @return Imagine\Filter\Transformation
     */
    public function resize(BoxInterface $size)
    {
        return $this->add(new Resize($size));
    }

    /**
     * Stacks a rotane transformation into the current transformations queue
     *
     * @param integer             $angle
     * @param Imagine\Image\Color $background
     *
     * @return Imagine\Filter\Transformation
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
     * @return Imagine\Filter\Transformation
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
     * @return Imagine\Filter\Transformation
     */
    public function show($format, array $options = array())
    {
        return $this->add(new Show($format, $options));
    }

    /**
     * Stacks a thumbnail transformation into the current transformation queue
     *
     * @param Imagine\Image\BoxInterface $size
     * @param string                     $mode
     *
     * @return Imagine\Filter\Transformation
     */
    public function thumbnail(BoxInterface $size, $mode = ImageInterface::THUMBNAIL_INSET)
    {
        return $this->add(new Thumbnail($size, $mode));
    }

    /**
     * Registers a given FilterInterface in an internal array of filters for
     * later application to an instance of ImageInterface
     *
     * @param Imagine\Filter\FilterInterface $filter
     *
     * @return Imagine\Filter\Transformation
     */
    public function add(FilterInterface $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }
}
