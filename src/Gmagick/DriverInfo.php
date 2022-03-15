<?php

namespace Imagine\Gmagick;

use Imagine\Driver\AbstractInfo;
use Imagine\Exception\NotSupportedException;
use Imagine\Image\Format;
use Imagine\Image\FormatList;

/**
 * Provide information and features supported by the Gmagick graphics driver.
 *
 * @since 1.3.0
 */
class DriverInfo extends AbstractInfo
{
    /**
     * @var static|\Imagine\Exception\NotSupportedException|null
     */
    private static $instance;

    private $availableMethods = array();

    /**
     * @throws \Imagine\Exception\NotSupportedException
     */
    protected function __construct()
    {
        if (!class_exists('Gmagick') || !extension_loaded('gmagick')) {
            throw new NotSupportedException('Gmagick driver not installed');
        }
        $m = null;
        $extensionVersion = phpversion('gmagick');
        $driverRawVersion = is_string($extensionVersion) ? $extensionVersion : '';
        $driverSemverVersion = preg_match('/^.*?(\d+\.\d+\.\d+)/', $driverRawVersion, $m) ? $m[1] : '';
        $gmagick = new \Gmagick();
        $engineVersion = $gmagick->getversion();
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
     * @see \Imagine\Driver\AbstractInfo::checkFeature()
     */
    protected function checkFeature($feature)
    {
        switch ($feature) {
            case static::FEATURE_COALESCELAYERS:
                throw new NotSupportedException('Gmagick does not support coalescing');
            case static::FEATURE_NEGATEIMAGE:
                if (!$this->isMethodAvailale('negateimage')) {
                    throw new NotSupportedException('Gmagick version 1.1.0 RC3 is required for negative effect');
                }
                break;
            case static::FEATURE_COLORIZEIMAGE:
                throw new NotSupportedException('Gmagick does not support colorize');
            case static::FEATURE_SHARPENIMAGE:
                throw new NotSupportedException('Gmagick does not support sharpen yet');
            case static::FEATURE_CONVOLVEIMAGE:
                if (!$this->isMethodAvailale('convolveimage')) {
                    throw new NotSupportedException('The version of Gmagick extension is too old: it does not support convolve (you need gmagick 2.0.1RC2 or later.');
                }
                break;
            case static::FEATURE_CUSTOMRESOLUTION:
                throw new NotSupportedException('Gmagick does not support setting custom resolutions');
            case static::FEATURE_GETCMYKCOLORSCORRECTLY:
                throw new NotSupportedException('Gmagick fails to read CMYK colors properly, see https://bugs.php.net/bug.php?id=67435');
            case static::FEATURE_TRANSPARENCY:
                throw new NotSupportedException("Gmagick doesn't support transparency");
            case static::FEATURE_ADDLAYERSTOEMPTYIMAGE:
                throw new NotSupportedException("Can't animate empty images because Gmagick is affected by bug https://bugs.php.net/bug.php?id=62309");
            case static::FEATURE_DETECTGRAYCOLORSPACE:
                throw new NotSupportedException('Gmagick does not support gray colorspace, because of the lack of image type support');
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
        $gmagick = new \Gmagick();
        $magickFormats = array_map('strtolower', $gmagick->queryFormats());
        foreach (Format::getAll() as $format) {
            if (in_array($format->getID(), $magickFormats, true) || array_intersect($magickFormats, $format->getAlternativeIDs()) !== array()) {
                $supportedFormats[] = $format;
            }
        }

        return new FormatList($supportedFormats);
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
