<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Profile;

interface ProfileInterface
{
    /**
     * Return the content of the profile
     * Inbuilt is binary string but various profiles can have specific parsers
     * to provide an improved interface
     *
     * @param bool $binary Set to true to get binary data
     *
     * @return string|mixed Binary string or specific to profile type
     */
    public function get($binary = false);

    /**
     * Create new profile
     *
     * @param string $name Name of profile (icc, exif)
     * @param mixed $content
     * @param false|\Imagick $owner Image where this profile instance is stored
     */
    public function __construct($name, $content, $owner = false);
}
