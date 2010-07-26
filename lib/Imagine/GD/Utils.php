<?php

namespace Imagine\GD;

/**
 * GD utility functions
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
class Utils
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
     * Checks whether the parameter is a valid GD image resource.
     *
     * @param resource $resource
     * @return boolean
     */
    public static function isResource($resource)
    {
        return (is_resource($resource) && 'gd' === get_resource_type($resource));
    }
}