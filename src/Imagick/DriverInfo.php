<?php

namespace Imagine\Imagick;

use Imagine\Driver\AbstractInfo;
use Imagine\Exception\NotSupportedException;
use Imagine\Image\Format;
use Imagine\Image\FormatList;

/**
 * Provide information and features supported by the Imagick graphics driver.
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
     * @var bool|null
     */
    private $colorProfilesSupported = null;

    /**
     * @var bool|null
     */
    private $colorspaceConversionAvailable = null;

    /**
     * @throws \Imagine\Exception\NotSupportedException
     */
    protected function __construct()
    {
        if (!class_exists('Imagick') || !extension_loaded('imagick')) {
            throw new NotSupportedException('Imagick driver not installed');
        }
        $m = null;
        $extensionVersion = phpversion('imagick');
        $driverRawVersion = is_string($extensionVersion) ? $extensionVersion : '';
        $driverSemverVersion = preg_match('/^.*?(\d+\.\d+\.\d+)/', $driverRawVersion, $m) ? $m[1] : '';
        $imagick = new \Imagick();
        $engineVersion = $imagick->getversion();
        if (is_array($engineVersion) && isset($engineVersion['versionString']) && is_string($engineVersion['versionString'])) {
            if (preg_match('/^.*?(\d+\.\d+\.\d+(-\d+)?(\s+Q\d+)?)/i', $engineVersion['versionString'], $m)) {
                $engineRawVersion = $m[1];
            } else {
                $engineRawVersion = $engineVersion['versionString'];
            }
        } else {
            $engineRawVersion = '';
        }
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
     * @see \Imagine\Driver\AbstractInfo::checkFeature()
     */
    protected function checkFeature($feature)
    {
        switch ($feature) {
            case static::FEATURE_COLORPROFILES:
                if (!$this->areColorProfilesSupported()) {
                    throw new NotSupportedException('Unable to manage color profiles: be sure to compile ImageMagick with the `--with-lcms2` option');
                }
                break;
            case static::FEATURE_COLORSPACECONVERSION:
                if (!$this->isColorspaceConversionAvailable()) {
                    throw new NotSupportedException('Your version of Imagick does not support colorspace conversions.');
                }
                break;
            case static::FEATURE_GRAYSCALEEFFECT:
                if (version_compare($this->getEngineVersion(), '6.8.5') <= 0) {
                    throw new NotSupportedException(sprintf('Converting an image to grayscale requires ImageMagick version 6.8.5 or higher is required, %s provided', $this->getEngineVersion()));
                }
                break;
            case static::FEATURE_CUSTOMRESOLUTION:
                // We can't do version_compare($this->getDriverVersion(), '3.1.0') < 0 because phpversion('imagick') may return @PACKAGE_VERSION@
                // @see https://www.php.net/manual/en/imagick.queryfontmetrics.php#101027
                // So, let's check ImagickDraw::setResolution (which has been introduced in 3.1.0b1
                if (!method_exists('ImagickDraw', 'setResolution')) {
                    throw new NotSupportedException(sprintf('Setting image resolution requires imagick version 3.1.0 or higher is required, %s provided', $this->getDriverVersion(true)));
                }
                break;
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
        $imagick = new \Imagick();
        $magickFormats = array_map('strtolower', $imagick->queryformats());
        foreach (Format::getAll() as $format) {
            if (in_array($format->getID(), $magickFormats, true) || array_intersect($magickFormats, $format->getAlternativeIDs()) !== array()) {
                $supportedFormats[] = $format;
            }
        }

        return new FormatList($supportedFormats);
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
