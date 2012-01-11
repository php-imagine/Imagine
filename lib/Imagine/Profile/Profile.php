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

class Profile implements ProfileInterface
{
    /**
     * Register over all hardcoded available profiles
     * that can be injected into images
     * @var array
     */
    protected static $_profiles = array(
        'icc' => array(
            'srgb' => 'sRGB_IEC61966-2-1_no_black_scaling.icc'
        ),
    );

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var false|\Imagick
     */
    protected $owner;

    /**
     * Create new profile
     *
     * @param string $name Name of profile (icc, exif)
     * @param mixed $content
     * @param false|\Imagick $owner Image where this profile instance is stored
     */
    public function __construct($name, $content, $owner = false) {
        $this->name = $name;
        $this->content = $content;
        $this->owner = $owner;
    }

    /**
     * Get profile content
     * @return mixed
     */
    public function get($binary = false) {
        return !$binary ? base64_encode($this->content) : $this->content;
    }

    /**
     * Check if this profile (name) matches a pattern
     *
     * @param string $pattern
     * @param bool $matchContent
     */
    public function matches($pattern, $matchContent = false) {
        $check = $matchContent ? $this->get(true) : $this->name;
        if ($pattern === '*') $pattern = '.*';
        return preg_match("#$pattern#", $check);
    }

    /**
     * Check if a profile exists
     *
     * @param string $type Type of profile; icc, exif, iptc etc
     * @param string $name Name of this specific profile: srgb, cmyk etc
     * @return bool
     */
    public static function has($type, $name) {
        return isset(static::$_profiles[$type][$name]);
    }

    /**
     * Register a new profile
     *
     * @param string $type
     * @param string $name
     * @param mixed $content
     */
    public static function register($type, $name, $path) {
        static::$_profiles[$type][$name] = $path;
    }

    /**
     * Get the actual profile $name of type $type
     *
     * @throws Exception
     *
     * @param string $type What type of profile, ICC, Exif etc
     * @param string $name The name of the profile, srgb, cmyk etc
     * @return string File contents of profile
     */
    public static function open($type, $name) {
        $icc = static::$_profiles['icc'];
        if (!static::has($type, $name))
            throw new \Exception("$type profile $name does not exist");

        $path = static::$_profiles[$type][$name];
        if (substr($path, 0, 1) !== '/') $path = __DIR__ . '/profiles/' . $path;
        return new self($type, file_get_contents($path));
    }
}
