<?php

namespace Imagine\Imagick;

use Imagine\Driver\Info;
use Imagine\Exception\NotSupportedException;
use Imagine\Image\Format;
use Imagine\Image\FormatList;
use Imagine\Image\Palette\PaletteInterface;

/**
 * Provide information and features supported by the Imagick graphics driver.
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
     * @var bool|null
     */
    private $colorProfilesSupported = null;

    /**
     * @var bool|null
     */
    private $colorspaceConversionAvailable = null;

    /**
     * @var \Imagine\Image\FormatList|null
     */
    private $supportedFormats = null;

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
            if (class_exists('Imagick') && extension_loaded('imagick')) {
                $extensionVersion = phpversion('imagick');
                $imagick = new \Imagick();
                $engineVersion = $imagick->getversion();
                self::$instance = new static(is_string($extensionVersion) ? $extensionVersion : '', is_array($engineVersion) ? $engineVersion : array());
            } else {
                self::$instance = null;
            }
        }
        if (self::$instance === null && $required) {
            throw new NotSupportedException('Imagick not installed');
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
        if (version_compare($this->getEngineVersion(), '6.2.9') < 0) {
            throw new NotSupportedException(sprintf('ImageMagick version 6.2.9 or higher is required, %s provided', $this->getEngineVersion()));
        }
        if ($this->getEngineVersion(true) === '7.0.7-32') {
            // https://github.com/php-imagine/Imagine/issues/689
            throw new NotSupportedException(sprintf('ImageMagick version %s has known bugs that prevent it from working', $this->getEngineVersion()));
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
            if (!$this->areColorProfilesSupported()) {
                throw new NotSupportedException('Unable to manage color profiles: be sure to compile ImageMagick with the `--with-lcms2` option');
            }
        }
        if ($features & static::FEATURE_COLORSPACECONVERSION) {
            if (!$this->isColorspaceConversionAvailable()) {
                throw new NotSupportedException('Your version of Imagick does not support colorspace conversions.');
            }
        }
        if ($features & static::FEATURE_GRAYSCALEEFFECT) {
            if (version_compare($this->getEngineVersion(), '6.8.5') <= 0) {
                throw new NotSupportedException(sprintf('Converting an image to grayscale requires ImageMagick version 6.8.5 or higher is required, %s provided', $this->getEngineVersion()));
            }
        }
        if ($features & static::FEATURE_CUSTOMRESOLUTION) {
            // We can't do version_compare($this->getDriverVersion(), '3.1.0') < 0 because phpversion('imagick') may return @PACKAGE_VERSION@
            // @see https://www.php.net/manual/en/imagick.queryfontmetrics.php#101027
            // So, let's check ImagickDraw::setResolution (which has been introduced in 3.1.0b1
            if (!method_exists('ImagickDraw', 'setResolution')) {
                throw new NotSupportedException(sprintf('Setting image resolution requires imagick version 3.1.0 or higher is required, %s provided', $this->getDriverVersion(true)));
            }
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
        $magickFormats = array_map('strtolower', \Imagick::queryFormats());
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
     * ImageMagick without the lcms delegate cannot handle profiles well.
     * This detection is needed because there is no way to directly check for lcms.
     *
     * @return bool
     */
    private function areColorProfilesSupported()
    {
        if ($this->colorProfilesSupported === null) {
            $imagick = new \Imagick();
            if (method_exists($imagick, 'profileImage')) {
                try {
                    $imagick->newImage(1, 1, new \ImagickPixel('#fff'));
                    $imagick->profileImage('icc', 'x');
                    $this->colorProfilesSupported = false;
                } catch (\ImagickException $exception) {
                    // If ImageMagick has support for profiles, it detects the invalid profile data 'x' and throws an exception.
                    $this->colorProfilesSupported = true;
                }
            } else {
                $this->colorProfilesSupported = false;
            }
        }

        return $this->colorProfilesSupported;
    }

    /**
     * @return bool
     */
    private function isColorspaceConversionAvailable()
    {
        if ($this->colorspaceConversionAvailable === null) {
            $this->colorspaceConversionAvailable = method_exists('Imagick', 'setColorspace');
        }

        return $this->colorspaceConversionAvailable;
    }
}
