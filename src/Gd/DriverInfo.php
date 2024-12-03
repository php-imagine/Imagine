<?php

namespace Imagine\Gd;

use Imagine\Driver\AbstractInfo;
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
class DriverInfo extends AbstractInfo
{
    /**
     * @var static|\Imagine\Exception\NotSupportedException|null
     */
    private static $instance;

    /**
     * @throws \Imagine\Exception\NotSupportedException
     */
    protected function __construct()
    {
        if (!function_exists('gd_info') || !defined('GD_VERSION')) {
            throw new NotSupportedException('Gd driver not installed');
        }
        $m = null;
        $driverRawVersion = PHP_VERSION;
        $driverSemverVersion = defined('PHP_MAJOR_VERSION') ? implode('.', array(PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION)) : '';
        $engineRawVersion = is_string(GD_VERSION) ? GD_VERSION : '';
        $engineSemverVersion = preg_match('/^.*?(\d+\.\d+\.\d+)/', $engineRawVersion, $m) ? $m[1] : '';
        parent::__construct($driverRawVersion, $driverSemverVersion, $engineRawVersion, $engineSemverVersion);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::get()
     */
    public static function get($required = true)
    {
        if (self::$instance === null) {
            try {
                self::$instance = new static();
            } catch (NotSupportedException $x) {
                self::$instance = $x;
            }
        }
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        if ($required) {
            throw self::$instance;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::checkVersionIsSupported()
     * @see \Imagine\Driver\AbstractInfo::checkVersionIsSupported()
     */
    public function checkVersionIsSupported()
    {
        parent::checkVersionIsSupported();
        if ($this->getEngineVersion() === '' || version_compare($this->getEngineVersion(), '2.0.1') < 0) {
            throw new NotSupportedException(sprintf('GD2 version %s or higher is required, %s provided', '2.0.1', GD_VERSION));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\AbstractInfo::checkFeature()
     */
    protected function checkFeature($feature)
    {
        switch ($feature) {
            case static::FEATURE_COLORPROFILES:
                throw new NotSupportedException('GD driver does not support color profiles');
            case static::FEATURE_TEXTFUNCTIONS:
                if (!function_exists('imageftbbox')) {
                    throw new NotSupportedException('GD is not compiled with FreeType support');
                }
                break;
            case static::FEATURE_MULTIPLELAYERS:
                throw new NotSupportedException('GD does not support layer sets');
            case static::FEATURE_CUSTOMRESOLUTION:
                if (!function_exists('imageresolution')) {
                    throw new NotSupportedException('GD driver for PHP older than 7.2 does not support setting custom resolutions');
                }
                break;
            case static::FEATURE_EXPORTWITHCUSTOMRESOLUTION:
                if (!function_exists('imageresolution')) {
                    throw new NotSupportedException('GD driver for PHP older than 7.2 does not support exporting images with custom resolutions');
                }
                break;
            case static::FEATURE_DRAWFILLEDCHORDSCORRECTLY:
                throw new NotSupportedException('The GD Drawer can NOT draw correctly filled chords');
            case static::FEATURE_DRAWUNFILLEDCIRCLESWITHTICHKESSCORRECTLY:
                throw new NotSupportedException('The GD Drawer can NOT draw correctly not filled circles with a thickness greater than 1');
            case static::FEATURE_DRAWUNFILLEDELLIPSESWITHTICHKESSCORRECTLY:
                throw new NotSupportedException('The GD Drawer can NOT draw correctly not filled ellipses with a thickness greater than 1');
            case static::FEATURE_ROTATEIMAGEWITHCORRECTSIZE:
                $vFrom = '5.5';
                $vTo = '7.1.11';
                if (version_compare($this->getDriverVersion(), $vFrom) >= 0 && version_compare($this->getDriverVersion(), $vTo) < 0) {
                    // see https://bugs.php.net/bug.php?id=65148
                    throw new NotSupportedException("The GD driver is affected by bug https://bugs.php.net/bug.php?id=65148 from PHP version {$vFrom} to PHP version {$vTo}.");
                }
                break;
            case static::FEATURE_EXPORTWITHCUSTOMJPEGSAMPLINGFACTORS:
                throw new NotSupportedException('The GD driver does not support JPEG sampling factors');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\AbstractInfo::buildSupportedFormats()
     */
    protected function buildSupportedFormats()
    {
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

        return new FormatList($supportedFormats);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::requirePaletteSupport()
     * @see \Imagine\Driver\AbstractInfo::requirePaletteSupport()
     */
    public function requirePaletteSupport(PaletteInterface $palette)
    {
        if (!($palette instanceof RGB)) {
            throw new NotSupportedException('GD driver only supports RGB colors');
        }
    }
}
