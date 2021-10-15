<?php

namespace Imagine\Gd;

use Imagine\Driver\Info;
use Imagine\Exception\NotSupportedException;
use Imagine\Image\Format;
use Imagine\Image\FormatList;
use Imagine\Image\Palette\PaletteInterface;
use Imagine\Image\Palette\RGB;

/**
 * Provide information and features supported by the GD graphics driver.
 *
 * @since 1.3.0
 */
class DriverInfo implements Info
{
    /**
     * @var static|null|false
     */
    private static $instance = false;

    /**
     * @var string
     */
    private $driverRawVersion;

    /**
     * @var string
     */
    private $driverSemverVersion;

    /**
     * @var string
     */
    private $engineRawVersion;

    /**
     * @var string
     */
    private $engineSemverVersion;

    /**
     * @var \Imagine\Image\FormatList|null
     */
    private $supportedFormats = null;

    /**
     * @param string $gdVersion
     */
    protected function __construct($gdVersion)
    {
        $m = null;
        $this->driverRawVersion = PHP_VERSION;
        $this->driverSemverVersion = defined('PHP_MAJOR_VERSION') ? implode('.', array(PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION)) : '';
        $this->engineRawVersion = is_string($gdVersion) ? $gdVersion : '';
        $this->engineSemverVersion = preg_match('/^.*?(\d+\.\d+\.\d+)/', $this->engineRawVersion, $m) ? $m[1] : '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::get()
     */
    public static function get($required = true)
    {
        if (self::$instance === false) {
            if (function_exists('gd_info') && defined('GD_VERSION')) {
                self::$instance = new static(GD_VERSION);
            } else {
                return self::$instance = null;
            }
        }
        if (self::$instance === null && $required) {
            throw new NotSupportedException('Gd not installed');
        }

        return self::$instance;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::checkVersionIsSupported()
     */
    public function checkVersionIsSupported()
    {
        if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300) {
            throw new NotSupportedException('Imagine requires PHP 5.3 or later');
        }
        if ($this->getEngineVersion() === '' || version_compare($this->getEngineVersion(), '2.0.1') < 0) {
            throw new NotSupportedException(sprintf('GD2 version %s or higher is required, %s provided', '2.0.1', GD_VERSION));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::getDriverVersion()
     */
    public function getDriverVersion($raw = false)
    {
        return $raw ? $this->driverRawVersion : $this->driverSemverVersion;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::getEngineVersion()
     */
    public function getEngineVersion($raw = false)
    {
        return $raw ? $this->engineRawVersion : $this->engineSemverVersion;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::requireFeature()
     */
    public function requireFeature($features)
    {
        $features = (int) $features;
        if ($features & static::FEATURE_COLORPROFILES) {
            throw new NotSupportedException('GD driver does not support color profiles');
        }
        if ($features & static::FEATURE_TEXTFUNCTIONS) {
            if (!function_exists('imageftbbox')) {
                throw new NotSupportedException('GD is not compiled with FreeType support');
            }
        }
        if ($features & static::FEATURE_MULTIPLELAYERS) {
            throw new NotSupportedException('GD does not support layer sets');
        }
        if ($features & static::FEATURE_CUSTOMRESOLUTION) {
            throw new NotSupportedException('GD does not support setting custom resolutions');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::hasFeature()
     */
    public function hasFeature($features)
    {
        try {
            $this->requireFeature($features);
        } catch (NotSupportedException $x) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::getSupportedFormats()
     */
    public function getSupportedFormats()
    {
        if ($this->supportedFormats !== null) {
            return $this->supportedFormats;
        }
        $supportedFormats = array();
        foreach (array(
            'gif' => Format::ID_GIF,
            'jpeg' => Format::ID_JPEG,
            'png' => Format::ID_PNG,
            'wbmp' => Format::ID_WBMP,
            'xbm' => Format::ID_XBM,
            'bmp' => Format::ID_BMP,
            'webp' => Format::ID_WEBP,
            'avif' => Format::ID_AVIF,
        ) as $suffix => $formatID) {
            if (function_exists("image{$suffix}") && function_exists("imagecreatefrom{$suffix}")) {
                $supportedFormats[] = Format::get($formatID);
            }
        }
        $this->supportedFormats = new FormatList($supportedFormats);

        return $this->supportedFormats;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::isFormatSupported()
     */
    public function isFormatSupported($format)
    {
        return $this->getSupportedFormats()->find($format) !== null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::checkPaletteSupport()
     */
    public function checkPaletteSupport(PaletteInterface $palette)
    {
        if (!($palette instanceof RGB)) {
            throw new NotSupportedException('GD driver only supports RGB colors');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::isPaletteSupported()
     */
    public function isPaletteSupported(PaletteInterface $palette)
    {
        try {
            $this->checkPaletteSupport($palette);
        } catch (NotSupportedException $x) {
            return false;
        }

        return true;
    }
}
