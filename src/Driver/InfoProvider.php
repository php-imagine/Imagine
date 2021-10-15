<?php

namespace Imagine\Driver;

/**
 * Interface implemented by classes that provide info about a graphics driver.
 *
 * @since 1.3.0
 */
interface InfoProvider
{
    /**
     * Get the info about this driver.
     *
     * @param bool $required when the driver is not available: if FALSE the function returns NULL, if TRUE the driver throws a \Imagine\Exception\NotSupportedException
     *
     * @return \Imagine\Driver\Info|null
     */
    public static function getDriverInfo($required = true);
}
