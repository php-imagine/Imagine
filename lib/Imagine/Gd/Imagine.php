<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\ImageInterface;
use Imagine\ImagineInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;

final class Imagine implements ImagineInterface
{
    /**
     * @var array
     */
    private $types = array(
        IMAGETYPE_GIF      => 'gif',
        IMAGETYPE_JPEG     => 'jpeg',
        IMAGETYPE_JPEG2000 => 'jpeg',
        IMAGETYPE_PNG      => 'png',
        IMAGETYPE_UNKNOWN  => 'unknown',
        IMAGETYPE_WBMP     => 'wbmp',
    );

    /**
     * @var Imagine\Gd\GdInterface
     */
    private $gd;

    /**
     * @param Imagine\Gd\GdInterface $gd
     */
    public function __construct(GdInterface $gd)
    {
        $this->gd = $gd;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImagineInterface::create()
     */
    public function create(BoxInterface $size, Color $color = null)
    {
        $width    = $size->getWidth();
        $height   = $size->getHeight();
        $resource = $this->gd->create($width, $height);

        $this->throwOrEnableTransparency($resource, 'Create operation failed');

        $color = $resource->colorToIndex(($color ? $color : new Color('fff')));

        if (false === $color) {
            throw new RuntimeException('Unable to allocate color');
        }

        if (false === $resource->filledrectangle(
            0, 0, $width, $height, $color
        )) {
            throw new RuntimeException('Could not set background color fill');
        }

        return new Image($this->gd, $resource);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImagineInterface::open()
     */
    public function open($path)
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException(
                sprintf('File %s doesn\'t exist', $path)
            );
        }

        $resource = $this->gd->open($path);

        $this->throwOrEnableTransparency(
            $resource,
            sprintf('Image "%s" could not be opened', $path)
        );

        return new Image($this->gd, $resource);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\ImagineInterface::load()
     */
    public function load($string)
    {
        $resource = $this->gd->load($string);

        $this->throwOrEnableTransparency(
            $resource,
            'An image could not be created from the given input'
        );

        return new Image($this->gd, $resource);
    }

    /**
     * Enables transparency on valid Imagine\Gd\ResourceInterface instances or
     * throw Imagine\Exception\InvalidArgumentException otherwise
     *
     * @throws Imagine\Exception\RuntimeException
     */
    private function throwOrEnableTransparency($resource, $message)
    {
        if (!$resource instanceof ResourceInterface) {
            throw new RuntimeException($message);
        }

        if (false === $resource->disableAlphaBlending() ||
            false === $resource->enableSaveAlpha()) {
            throw new RuntimeException(
                'Could not set alphablending, savealpha and antialias values'
            );
        }
    }


    /**
     * (non-PHPdoc)
     * @see Imagine\ImagineInterface::font()
     */
    public function font($file, $size, Color $color)
    {
        return new Font($file, $size, $color);
    }
}
