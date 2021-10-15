<?php

namespace Imagine\Driver;

use Imagine\Image\Palette\PaletteInterface;

/**
 * Provide information and features supported by a graphics driver.
 *
 * @since 1.3.0
 */
interface Info
{
    /**
     * Affected functions: Imagine\Image\ImageInterface::profile(), Imagine\Image\ImageInterface::usePalette().
     *
     * @var int
     */
    const FEATURE_COLORPROFILES = 0x1;

    /**
     * Affected functions: Imagine\Image\ImageInterface::usePalette(), opening images with particular colorspaces.
     *
     * See also the checkPaletteSupport/isPaletteSupported driver info methods.
     *
     * @var int
     */
    const FEATURE_COLORSPACECONVERSION = 0x2;

    /**
     * Affected functions: Imagine\Effects\EffectsInterface::grayscale().
     *
     * @var int
     */
    const FEATURE_GRAYSCALEEFFECT = 0x4;

    /**
     * Affected functions: Imagine\Image\LayersInterface::coalesce().
     *
     * See also the FEATURE_MULTIPLELAYERS feature
     *
     * @var int
     */
    const FEATURE_COALESCELAYERS = 0x8;

    /**
     * Affected functions: Imagine\Effects\EffectsInterface::negative().
     *
     * @var int
     */
    const FEATURE_NEGATEIMAGE = 0x10;

    /**
     * Affected functions: Imagine\Effects\EffectsInterface::colorize().
     *
     * @var int
     */
    const FEATURE_COLORIZEIMAGE = 0x20;

    /**
     * Affected functions: Imagine\Effects\EffectsInterface::sharpen().
     *
     * @var int
     */
    const FEATURE_SHARPENIMAGE = 0x40;

    /**
     * Affected functions: Imagine\Effects\EffectsInterface::convolve().
     *
     * @var int
     */
    const FEATURE_CONVOLVEIMAGE = 0x80;

    /**
     * Affected functions: Imagine\Draw\DrawerInterface::text() and Imagine\Image\FontInterface::box().
     *
     * @var int
     */
    const FEATURE_TEXTFUNCTIONS = 0x100;

    /**
     * Affected functions: Imagine\Image\LayersInterface methods that would create more that 1 layer or 0 layers.
     *
     * @var int
     */
    const FEATURE_MULTIPLELAYERS = 0x200;

    /**
     * Affected functions: none at the moment.
     *
     * @var int
     */
    const FEATURE_CUSTOMRESOLUTION = 0x400;

    /**
     * Get the Info instance for a specific driver.
     *
     * @param bool $required when the driver is not available: if FALSE the function returns NULL, if TRUE the driver throws a \Imagine\Exception\NotSupportedException
     *
     * @throws \Imagine\Exception\NotSupportedException if $required is TRUE and the driver is not available
     *
     * @return static|null return NULL if the driver is not available and $required is FALSE
     */
    public static function get($required = true);

    /**
     * Check if the current driver/engine version combination is supported.
     *
     * @throws \Imagine\Exception\NotSupportedException if the version combination is not supported
     */
    public function checkVersionIsSupported();

    /**
     * Get the version of the driver.
     * For example:
     * - for GD: it's the version of PHP
     * - for gmagick: it's the version of the gmagick PHP extension
     * - for imagick: it's the version of the imagick PHP extension.
     *
     * @param bool $raw if false the result will be in the format <major>.<minor>.<patch>, if TRUE the result will be the raw version
     */
    public function getDriverVersion($raw = false);

    /**
     * Get the version of the library used by the driver.
     * For example:
     * - for GD: it's the version of libgd
     * - for gmagick: it's the version of the GraphicsMagick
     * - for imagick: it's the version of the ImageMagick.
     *
     * @param bool $raw if false the result will be in the format <major>.<minor>.<patch>, if TRUE the result will be the raw version
     */
    public function getEngineVersion($raw = false);

    /**
     * Check if the driver the features requested.
     *
     * @param int $features A combination of one or more of the FEATURE_... values
     *
     * @return bool returns TRUE if the driver supports all the specified features, FALSE otherwise
     */
    public function hasFeature($features);

    /**
     * Check if the driver has the features requested.
     *
     * @param int $features A combination of one or more of the FEATURE_... values
     *
     * @throws \Imagine\Exception\NotSupportedException if any of the requested features is not supported
     */
    public function requireFeature($features);

    /**
     * Get the list of supported file formats.
     *
     * @return \Imagine\Image\FormatList
     */
    public function getSupportedFormats();

    /**
     * Check if a format is supported.
     *
     * @param \Imagine\Image\Format|string $format
     *
     * @return bool
     */
    public function isFormatSupported($format);

    /**
     * Check if a palette is supported.
     *
     * @param \Imagine\Image\Palette\PaletteInterface $palette
     *
     * @throws \Imagine\Exception\NotSupportedException if the palette is not supported
     */
    public function checkPaletteSupport(PaletteInterface $palette);

    /**
     * Check if a palette is supported.
     *
     * @param \Imagine\Image\Palette\PaletteInterface $palette
     *
     * @return bool
     */
    public function isPaletteSupported(PaletteInterface $palette);
}
