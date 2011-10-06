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

use Imagine\Exception\InvalidArgumentException;
use Imagine\Filter\Basic\ApplyMask;
use Imagine\Filter\Basic\Copy;
use Imagine\Filter\Basic\Crop;
use Imagine\Filter\Basic\Fill;
use Imagine\Filter\Basic\FlipVertically;
use Imagine\Filter\Basic\FlipHorizontally;
use Imagine\Filter\Basic\Paste;
use Imagine\Filter\Basic\Resize;
use Imagine\Filter\Basic\Rotate;
use Imagine\Filter\Basic\Save;
use Imagine\Filter\Basic\Show;
use Imagine\Filter\Basic\Thumbnail;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\ImageFactoryInterface;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\Fill\FillInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

final class Transformation implements FilterInterface, ManipulatorInterface
{
    /**
     * @var array
     */
    private $filters = array();

    /**
     * An ImagineInterface instance.
     *
     * @var ImagineInterface
     */
    private $imagine;

    /**
     * Class constructor.
     *
     * @param ImagineInterface $imagine An ImagineInterface instance
     */
    public function __construct(ImagineInterface $imagine = null)
    {
        $this->imagine = $imagine;
    }

    /**
     * Applies a given FilterInterface onto given ImageInterface and returns
     * modified ImageInterface
     *
     * @param Imagine\Filter\FilterInterface $filter
     * @param Imagine\Image\ImageInterface   $image
     *
     * @return Imagine\Image\ImageInterface
     * @throws Imagine\Exception\InvalidArgumentException
     */
    public function applyFilter(ImageInterface $image, FilterInterface $filter)
    {
        if ($filter instanceof ImagineAware) {
            if (!$this->imagine instanceof ImagineInterface) {
                throw new InvalidArgumentException(sprintf('In order to use %s pass an Imagine\Image\ImagineInterface instance to Transformation constructor', get_class($filter)));
            }
            $filter->setImagine($this->imagine);
        }
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
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::copy()
     */
    public function copy()
    {
        return $this->add(new Copy());
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::crop()
     */
    public function crop(PointInterface $start, BoxInterface $size)
    {
        return $this->add(new Crop($start, $size));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::flipHorizontally()
     */
    public function flipHorizontally()
    {
        return $this->add(new FlipHorizontally());
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::flipVertically()
     */
    public function flipVertically()
    {
        return $this->add(new FlipVertically());
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::paste()
     */
    public function paste(ImageInterface $image, PointInterface $start)
    {
        return $this->add(new Paste($image, $start));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::applyMask()
     */
    public function applyMask(ImageInterface $mask)
    {
        return $this->add(new ApplyMask($mask));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::fill()
     */
    public function fill(FillInterface $fill)
    {
        return $this->add(new Fill($fill));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::resize()
     */
    public function resize(BoxInterface $size)
    {
        return $this->add(new Resize($size));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::rotate()
     */
    public function rotate($angle, Color $background = null)
    {
        return $this->add(new Rotate($angle, $background));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::save()
     */
    public function save($path, array $options = array())
    {
        return $this->add(new Save($path, $options));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::show()
     */
    public function show($format, array $options = array())
    {
        return $this->add(new Show($format, $options));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::thumbnail()
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
