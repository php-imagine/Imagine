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
    const FEATURE_COLORPROFILES = 1;

    /**
     * Affected functions: Imagine\Image\ImageInterface::usePalette(), opening images with particular colorspaces.
     *
     * See also the requirePaletteSupport/isPaletteSupported driver info methods.
     *
     * @var int
     */
    const FEATURE_COLORSPACECONVERSION = 2;

    /**
     * Affected functions: Imagine\Effects\EffectsInterface::grayscale().
     *
     * @var int
     */
    const FEATURE_GRAYSCALEEFFECT = 3;

    /**
     * Affected functions: Imagine\Image\LayersInterface::coalesce().
     *
     * See also the FEATURE_MULTIPLELAYERS feature
     *
     * @var int
     */
    const FEATURE_COALESCELAYERS = 4;

    /**
     * Affected functions: Imagine\Effects\EffectsInterface::negative().
     *
     * @var int
     */
    const FEATURE_NEGATEIMAGE = 5;

    /**
     * Affected functions: Imagine\Effects\EffectsInterface::colorize().
     *
     * @var int
     */
    const FEATURE_COLORIZEIMAGE = 6;

    /**
     * Affected functions: Imagine\Effects\EffectsInterface::sharpen().
     *
     * @var int
     */
    const FEATURE_SHARPENIMAGE = 7;

    /**
     * Affected functions: Imagine\Effects\EffectsInterface::convolve().
     *
     * @var int
     */
    const FEATURE_CONVOLVEIMAGE = 8;

    /**
     * Affected functions: Imagine\Draw\DrawerInterface::text() and Imagine\Image\FontInterface::box().
     *
     * @var int
     */
    const FEATURE_TEXTFUNCTIONS = 9;

    /**
     * Affected functions: Imagine\Image\LayersInterface methods that would create more that 1 layer or 0 layers.
     *
     * @var int
     */
    const FEATURE_MULTIPLELAYERS = 10;

    /**
     * Affected functions: none at the moment.
     *
     * @var int
     */
    const FEATURE_CUSTOMRESOLUTION = 11;

    /**
     * Affected functions: Imagine\Image\ImageInterface::get(), Imagine\Image\ImageInterface::save(), Imagine\Image\ImageInterface::show().
     *
     * @var int
     */
    const FEATURE_EXPORTWITHCUSTOMRESOLUTION = 12;

    /**
     * Affected functions: Imagine\Draw\DrawerInterface::chord() with $fill == true.
     *
     * @var int
     */
    const FEATURE_DRAWFILLEDCHORDSCORRECTLY = 13;

    /**
     * Affected functions: Imagine\Draw\DrawerInterface::circle() with $fill == false and $thickness > 1.
     *
     * @var int
     */
    const FEATURE_DRAWUNFILLEDCIRCLESWITHTICHKESSCORRECTLY = 14;

    /**
     * Affected functions: Imagine\Draw\DrawerInterface::ellipse() with $fill == false and $thickness > 1.
     *
     * @var int
     */
    const FEATURE_DRAWUNFILLEDELLIPSESWITHTICHKESSCORRECTLY = 15;

    /**
     * Affected functions: Imagine\Image\ImageInterface::getColorAt() when the palette is CMYK.
     *
     * @var int
     */
    const FEATURE_GETCMYKCOLORSCORRECTLY = 16;

    /**
     * Affected functions: any that uses colors with an alpha channel.
     *
     * @var int
     */
    const FEATURE_TRANSPARENCY = 17;

    /**
     * Affected functions: Imagine\Image\ImageInterface::rotate(), Imagine\Filter\Basic\Rotate.
     *
     * @var int
     */
    const FEATURE_ROTATEIMAGEWITHCORRECTSIZE = 18;

    /**
     * Affected functions: Imagine\Image\ImageInterface::get(), Imagine\Image\ImageInterface::save(), Imagine\Image\ImageInterface::show().
     *
     * @var int
     */
    const FEATURE_EXPORTWITHCUSTOMJPEGSAMPLINGFACTORS = 19;

    /**
     * Adding frames to a image with no previously loaded layers works.
     *
     * @var int
     */
    const FEATURE_ADDLAYERSTOEMPTYIMAGE = 20;

    /**
     * Affected functions: Imagine\Image\ImagineInterface::open(), Imagine\Image\ImagineInterface::load(), Imagine\Image\ImagineInterface::read().
     *
     * @var int
     */
    const FEATURE_DETECTGRAYCOLORSPACE = 21;

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
     * @param int|int[] $features The features to be checked (see the Info::FEATURE_... constants)
     *
     * @return bool returns TRUE if the driver supports all the specified features, FALSE otherwise
     */
    public function hasFeature($features);

    /**
     * Check if the driver has the features requested.
     *
     * @param int|int[] $features The features to be checked (see the Info::FEATURE_... constants)
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
    public function requirePaletteSupport(PaletteInterface $palette);

    /**
     * Check if a palette is supported.
     *
     * @param \Imagine\Image\Palette\PaletteInterface $palette
     *
     * @return bool
     */
    public function isPaletteSupported(PaletteInterface $palette);
}
