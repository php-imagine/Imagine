<?php

namespace Imagine\GD;

use Imagine\Image;

/**
 * GD utility functions
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
class Utils extends \Imagine\Utils
{
    /**
     * Mapping of image types to function name components.
     */
    protected static $typeMap = array(
        \IMAGETYPE_GIF  => 'gif',
        \IMAGETYPE_JPEG => 'jpeg',
        \IMAGETYPE_PNG  => 'png',
    );

    /**
     * Get the load function for an image type.
     *
     * @param int $type
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getLoadFunction($type)
    {
        if (isset(self::$typeMap[$type])) {
            return 'imagecreatefrom' . self::$typeMap[$type];
        } else {
            throw new \InvalidArgumentException('Unsupported image type: ' . $type);
        }
    }

    /**
     * Get the save function for an image type.
     *
     * @param int $type
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getSaveFunction($type)
    {
        if (isset(self::$typeMap[$type])) {
            return 'image' . self::$typeMap[$type];
        } else {
            throw new \InvalidArgumentException('Unsupported image type: ' . $type);
        }
    }

    /**
     * Get the save quality for an image type.
     *
     * @param int $type
     * @param int $quality between 0 and 100 inclusive
     * @return int
     * @throws \InvalidArgumentException
     */
    public static function getSaveQuality($type, $quality)
    {
        if($quality < 0 || $quality > 100) {
            throw new \InvalidArgumentException(sprintf('Save quality must be comprised between 0 and 100 inclusive, "%d" given'));
        }

        if(IMAGETYPE_PNG === $type) {
            // Transform quality to PNG compression level
            $quality = abs(($quality - 100) / 11.111111);
        }

        return round($quality);
    }

    /**
     * Checks whether the parameter is a valid GD image resource.
     *
     * @param resource $resource
     * @return boolean
     */
    public static function isResource($resource)
    {
        return (is_resource($resource) && 'gd' === get_resource_type($resource));
    }

    /**
     * Creates a new GD image resource.
     *
     * If $type is given, it will be used to initialize type-specific settings
     * on the resource, such as PNG alpha channel support.
     *
     * @param int $width
     * @param int $height
     * @param int $type
     * @return resource
     */
    public static function createResource($width, $height, $type = null)
    {
        if (false === ($resource = imagecreatetruecolor($width, $height))) {
            throw new \RuntimeException('Could not create image resource');
        }

        if ($type == \IMAGETYPE_PNG) {
            if (! imagealphablending($resource, false)) {
                throw new \RuntimeException('Could not set alpha blending');
            }

            if (! imagesavealpha($resource, true)) {
                throw new \RuntimeException('Could not toggle saving of alpha channel');
            }
        }

        return $resource;
    }
}
