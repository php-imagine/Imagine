<?php

namespace Imagine;

// TODO: Allow adapter library to be selected upon construction
use Imagine\GD\Utils as GDUtls;

/**
 * Image
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
class Image
{
    /**
     * Image path
     *
     * @var string
     */
    protected $path;

    /**
     * Image width
     *
     * @var int
     */
    protected $width;

    /**
     * Image height
     *
     * @var int
     */
    protected $height;

    /**
     * Image type
     *
     * @var int
     */
    protected $type;

    /**
     * MIME type
     *
     * @var string
     */
    protected $mimeType;

    /**
     * Image load function
     *
     * @var callable
     */
    protected $loadFunction;

    /**
     * Image resource
     *
     * @var resource
     */
    protected $resource;

    /**
     * Constructs an image from a file path.
     *
     * @param string $path
     * @throws \InvalidArgumentException
     */
    public function __construct($path)
    {
        if (false === ($size = @getimagesize($path))) {
            throw new \InvalidArgumentException('Could not determine image info for: ' . $path);
        }

        $this->path = $path;
        $this->width = $size[0];
        $this->height = $size[1];
        $this->type = $size[2];
        $this->mimeType = $size['mime'];
        $this->loadFunction = GDUtls::getLoadFunction($this->type);
    }

    /**
     * Get image type
     *
     * @link http://php.net/manual/en/image.constants.php
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get MIME type
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Get image height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get image width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get image path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get image resource
     *
     * @return resource
     * @throws \RuntimeException
     */
    public function getResource()
    {
        if (! isset($this->resource)) {
            if (false === ($this->resource = call_user_func($this->loadFunction, $this->path))) {
                throw new \RuntimeException('Could not load image: ' . $this->path);
            }
        }
        return $this->resource;
    }

    /**
     * Set image resource
     *
     * @param resource $resource
     * @throws \InvalidArgumentException
     */
    public function setResource($resource)
    {
        if (! GDUtls::isResource($resource)) {
            throw new \InvalidArgumentException('Invalid resource');
        }

        if (GDUtls::isResource($this->resource)) {
            imagedestroy($this->resource);
        }

        $this->resource = $resource;
        $this->width = imagesx($this->resource);
        $this->height = imagesy($this->resource);
    }

    /**
     * Free the image resource if possible.
     */
    public function __destruct()
    {
        if (GDUtls::isResource($this->resource)) {
            imagedestroy($this->resource);
        }
    }

}