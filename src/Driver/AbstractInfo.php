<?php

namespace Imagine\Driver;

use Imagine\Exception\NotSupportedException;
use Imagine\Image\Palette\PaletteInterface;

/**
 * Base class for the default DriverInfo classes.
 *
 * @since 1.3.0
 */
abstract class AbstractInfo implements Info
{
    /**
     * @var static|\Imagine\Exception\NotSupportedException|null
     */
    private static $instance;

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
     * @param string $driverRawVersion
     * @param string $driverSemverVersion
     * @param string $engineRawVersion
     * @param string $engineSemverVersion
     */
    protected function __construct($driverRawVersion, $driverSemverVersion, $engineRawVersion, $engineSemverVersion)
    {
        $this->driverRawVersion = $driverRawVersion;
        $this->driverSemverVersion = $driverSemverVersion;
        $this->engineRawVersion = $engineRawVersion;
        $this->engineSemverVersion = $engineSemverVersion;
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
     * Check if the driver has a specific feature.
     *
     * @param int $feature The feature to be checked (see the Info::FEATURE_... constants)
     *
     * @throws \Imagine\Exception\NotSupportedException if any of the requested features is not supported
     */
    abstract protected function checkFeature($feature);

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::checkVersionIsSupported()
     */
    public function checkVersionIsSupported()
    {
        if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50500) {
            throw new NotSupportedException('Imagine requires PHP 5.5 or later');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::requireFeature()
     */
    public function requireFeature($features)
    {
        $features = array_map('intval', is_array($features) ? $features : array($features));
        foreach ($features as $feature) {
            $this->checkFeature($feature);
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
     * Build the list of supported file formats.
     *
     * @return \Imagine\Image\FormatList
     */
    abstract protected function buildSupportedFormats();

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\Info::getSupportedFormats()
     */
    public function getSupportedFormats()
    {
        if ($this->supportedFormats === null) {
            $this->supportedFormats = $this->buildSupportedFormats();
        }

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
     * @see \Imagine\Driver\Info::requirePaletteSupport()
     */
    public function requirePaletteSupport(PaletteInterface $palette)
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
            $this->requirePaletteSupport($palette);
        } catch (NotSupportedException $x) {
            return false;
        }

        return true;
    }
}
