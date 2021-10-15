<?php

namespace Imagine\Gmagick;

use Imagine\Driver\Info;
use Imagine\Exception\NotSupportedException;
use Imagine\Image\Format;
use Imagine\Image\FormatList;
use Imagine\Image\Palette\PaletteInterface;

/**
 * Provide information and features supported by the Gmagick graphics driver.
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

    private $availableMethods = array();

    /**
     * @param string $driverRawVersion
     * @param array $engineRawVersion
     */
    protected function __construct($driverRawVersion, array $engineRawVersion)
    {
        $m = null;
        $this->driverRawVersion = (string) $driverRawVersion;
        $this->driverSemverVersion = preg_match('/^.*?(\d+\.\d+\.\d+)/', $this->driverRawVersion, $m) ? $m[1] : '';
        $this->engineRawVersion = '';
        if (isset($engineRawVersion['versionString']) && is_string($engineRawVersion['versionString'])) {
            if (preg_match('/^.*?(\d+\.\d+\.\d+(-\d+)?(\s+Q\d+)?)/i', $engineRawVersion['versionString'], $m)) {
                $this->engineRawVersion = $m[1];
            } else {
                $this->engineRawVersion = $engineRawVersion['versionString'];
            }
        }
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
            if (class_exists('Gmagick') && extension_loaded('gmagick')) {
                $extensionVersion = phpversion('gmagick');
                $gmagick = new \Gmagick();
                $engineVersion = $gmagick->getversion();
                self::$instance = new static(is_string($extensionVersion) ? $extensionVersion : '', is_array($engineVersion) ? $engineVersion : array());
            } else {
                self::$instance = null;
            }
        }
        if (self::$instance === null && $required) {
            throw new NotSupportedException('Gmagick not installed');
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
        if ($features & static::FEATURE_COALESCELAYERS) {
            throw new NotSupportedException('Gmagick does not support coalescing');
        }
        if ($features & static::FEATURE_NEGATEIMAGE) {
            if (!$this->isMethodAvailale('negateimage')) {
                throw new NotSupportedException('Gmagick version 1.1.0 RC3 is required for negative effect');
            }
        }
        if ($features & static::FEATURE_COLORIZEIMAGE) {
            throw new NotSupportedException('Gmagick does not support colorize');
        }
        if ($features & static::FEATURE_SHARPENIMAGE) {
            throw new NotSupportedException('Gmagick does not support sharpen yet');
        }
        if ($features & static::FEATURE_CONVOLVEIMAGE) {
            if (!$this->isMethodAvailale('convolveimage')) {
                throw new NotSupportedException('The version of Gmagick extension is too old: it does not support convolve (you need gmagick 2.0.1RC2 or later.');
            }
        }
        if ($features & static::FEATURE_CUSTOMRESOLUTION) {
            throw new NotSupportedException('Gmagick does not support setting custom resolutions');
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
        $gmagick = new \Gmagick();
        $magickFormats = array_map('strtolower', $gmagick->queryFormats());
        foreach (Format::getAll() as $format) {
            if (in_array($format->getID(), $magickFormats, true) || array_intersect($magickFormats, $format->getAlternativeIDs()) !== array()) {
                $supportedFormats[] = $format;
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

    /**
     * @param string $methodName
     *
     * @return bool
     */
    private function isMethodAvailale($methodName)
    {
        if (!isset($this->availableMethods[$methodName])) {
            $gmagick = new \Gmagick();
            $this->availableMethods[$methodName] = method_exists($gmagick, $methodName);
        }

        return $this->availableMethods[$methodName];
    }
}
