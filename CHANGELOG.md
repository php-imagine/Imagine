# CHANGELOG

### NEXT (YYYY-MM-DD)
  * Remove `final` from classes and methods: we'll use semver in case of breaking changes (@mlocati)

### 1.0.0-alpha1 (2018-08-28)
  * Imagine is now tested under Windows too (@mlocati)
  * Add support to webp image format (@chregu, @antoligy, @alexander-schranz)
  * Add `Imagine\File\LoaderInterface` that allows loading remote images with any imaging driver (@mlocati).
    You can use your own `LoaderInterface` implementation so that you can for instance use curl or any other library.
  * Fix some phpdoc issues (@mlocati)
  * `flipHorizontally` and `flipVertically` methods of GD images is now much faster on PHP 5.5+ (@mlocati)
  * Fix loading of PNG indexed images with GD (@mlocati)
  * Loading indexed images with GD is now much faster on PHP 5.5+ (@mlocati)
  * Add support to grayscale images with Gmagick (@mlocati)
  * Add support to alpha channels of Gmagick images (@mlocati)
  * Fix `getColorAt` method of Gmagick images (@mlocati)
  * Add `getTransformations` to the `Autorotate` filter, so that you can get the list of transformations that should be applied to an image accordingly to the EXIF metadata (@mlocati)
  * The metadata reader now doesn't throw exceptions or warnings (@lentex, @mlocati)
  * Fix documentation (@ZhangChaoWN, @Mark-H, @mlocati)
  * Fix pixel range issue with Gmagick image (@b-viguier)
  * Fix `text` drawer method on Windows when using relative font file paths (@mlocati)
  * Fix `box` font method on Windows when using relative font file paths (@mlocati)
  * Fix crash on Windows when loading an image with Imagick (@mlocati)
  * Fix generation of API documentation (@mlocati)
  * Add `jpeg_sampling_factors` option when saving JPEG images (Gmagick/Imagick only) (@ausi)
  * Add BMP as supported image format (@mlocati)
  * Add support to new image type constants of Imagick (@ausi)
  * Check that Imagick correctly supports profiles (@ausi)
  * Add `setMetadataReader`/`getMetadataReader` to `ImagineInterface` (@mlocati)  
    **BREAKING CHANGE** if you have your own `ImagineInterface` implementation, it now must implement those two methods
  * Fix creating Gmagick images with alpha colors when palette doesn't support alpha (@FractalizeR)
  * Fix warning about deprecated clone method in copy method of Imagick images (@mlocati)
  * Fix copy methods of Images (the original image and its new copy are now fully detached) (@mlocati)
  * It's now possible to use `clone $image` as an alternative to `$image->copy()` (@mlocati)
  * Add support to custom classes for `BoxInterface`, `MetadataReaderInterface`, `FontInterface`, `LoaderInterface`, `LayersInterface`, `ImageInterface` (@mlocati)  
    **BREAKING CHANGE** if you have your own `ImagineInterface` implementation, it now must implement the methods of `ClassFactoryAwareInterface`
  * Add support for pasting with alpha for GD and Imagick (@AlloVince, @mlocati)
  * Downscaling a `Box` until it reaches a dimension less than 1 returns a box with dimension of 1 instead of throwing an exception (@mlocati)    
    **BREAKING CHANGE** if you relied on `Box::scale` throwing an exception in this case
  * New filters: `BlackWhite`, `BorderDetection`, `Negation`, `Neighborhood` (@rejinka)
  * Minor optimization of filters based on `OnPixelBased` (@rejinka, @mlocati)
  * Add flag to `thumbnail` to allow upscaling images (@vlakoff)  
    **NOTE** the `$mode` argument has been renamed to `$settings`, and it's now an integer (but old string values are accepted for backward compatibility)
  * New filter: `brightness` (@lenybernard, @mlocati)
  * New filter: `colvolve` available for all graphics libraries except gmagick with version prior to 2.0.1RC2 (@armatronic, @mlocati)
  * Fix bug in Imagine\Image\Palette\RGB::blend() (@dmolineus, @mlocati)
  * Autoload was moved from PSR-0 to PSR-4, and code files moved from `/lib/Imagine` to `/src` (@mlocati)

### 0.7.1 (2017-05-16)
  * Remove Symfony PHPUnit bridge as dependency (@craue)

### 0.7.0 (2017-05-02)
  * Fix memory usage on metadata reading (@Seldaek)
  * PHP 7.1 support
  * Latest Imagemagick compatibility (@jdewit)

### 0.6.3 (2015-09-19)
  * Fix wrong array_merge when calling Transformation::getFilters without filters
  * Add export-ignore git attribute (@Benoth)
  * Fix docblocks (@Sm0ke0ut and @norkunas)
  * Fix animated gif loop length options (@jygaulier)
  * Multiple tweaks for the repository and travis builds (@localheinz, @vrkansagara and @dunzun)
  * Fix metadata extraction from streams (@armatronic)
  * Fix autorotation (@tarleb)
  * Load exifmetadata reader whenever possible
  * Add metadata getter

### 0.6.2 (2014-11-11)
  * Stripping image containing an invalid ICC profile fails
  * MetadataBag now implements \Countable
  * Fix wrong array_merge in MetadataBag giving invalid results with HTTP resources (@javaguirre)
  * Fix Imagick merge strategy (@GrahamCampbell)
  * Fixed various alpha issues (@RadekDvorak)
  * Fix Image cloning on HHVM (@RdeWilde)
  * Fix exception on invalid file using GD driver (@vlakoff).
  * Fix ImageInterface::getSize on animated GIFs (@sokac)

### 0.6.1 (2014-06-16)
  * Fix invalid namespace usage (#336 @csanquer).

### 0.6.0 (2014-06-13)

  * BC break: Colors are now provided through the PaletteInterface. Any call
    to previous Imagine\Image\Color constructor must be removed and use the
    palette provided by Imagine\Image\ImageInterface::getPalette to create
    colors.
  * BC break : Animated GIF default delay is no longer 800ms but null. This
    avoids resettings a delay on animated image.
  * Add support for ICC profiles
  * Add support for CMYK and grayscale colorspace images.
  * Add filter argument to ImageInterface::thumbnail method.
  * Add priority to filters (@Richtermeister).
  * Add blur effect (@Nokrosis).
  * Rename "quality" option to "jpeg_quality" and apply it only to JPEG files (@vlakoff).
  * Add "png_compression_level" option (@vlakoff).
  * Rename "filters" option to "png_compression_filter" (@vlakoff).
  * Deprecate `quality` and `filters` ManipulatorInterface::save options, use
    `jpeg_quality`, `png_compression_level` and `png_compression_filter` instead.
  * Add support for alpha blending in GD drawer (@salem).
  * Add width parameter to Drawer::text (@salemgolemugoo).
  * Add NotSupportedException when a driver does not support an operation (@rouffj).
  * Add support for metadata.
  * Fix #158: GD alpha detection + Color::isOpaque are broken.
  * Fix color extraction for non-RGB palettes.

### 0.5.0 (2013-07-10)

  * Add `Layers::coalesce`.
  * Add filter option to `ImageInterface::resize`.
  * Add sharpen effect.
  * Add interlace support.
  * `LayersInterface` now extends `ArrayAccess`, gives support for animated gifs.
  * Remove Imagick and Gmagick flatten after composite.
  * Fix pixel opacity reading in `Gmagick::histogram`.
  * Deprecate pear channel installation.
  * Deprecate phar installation.

### 0.4.1 (2012-12-13)

  * Lazy-load GD layers.

### 0.4.0 (2012-12-10)

  * Add support for image Layers.
  * Add Colorize effect.
  * Add documentation for the Grayscale effect.
  * Port RelativeResize filter from JmikolaImagineBundle.

### 0.3.1 (2012-11-12)

  * Add Grayscale effect.
  * `Drawer::text` position fix.

### 0.3.0 (2012-07-28)

  * Add configurable border thickness to drawer interface and implementations.
  * Add `ImageInterface`::strip.
  * Add Canvas filter.
  * Add resolution option on image saving.
  * Add Grayscale filter.
  * Add sami API documentation.
  * Add compression quality to Gmagick.
  * Add effects API.
  * Add method to get pixel at point in Gmagick.
  * Ensure valid background color in rotations.
  * Fill lines with color to prevent semi-transparency issues.
  * Use `Imagick::resizeImage` instead of `Imagick::thumbnailImage` for resizing.
  * Fix PNG transparency on save ; do not flatten if not necessary.

### 0.2.8 (2011-11-29)

  * Add support for Travis CI.

### 0.2.7 (2011-11-17)

  * Use composer for autoloading.

### 0.2.6 (2011-11-09)

  * Documentation enhancements.

### 0.2.5 (2011-10-29)

  * Add PEAR support.
  * Documentation enhancements.

### 0.2.4 (2011-10-17)

  * Add imagine.phar, phar and rake tasks.
  * Add `ImagineInterface::read` to read from a stream resource.
  * Documentation enhancements.
  * Fix gifs transparency issues.

### 0.2.3 (2011-10-16)

  * Documentation enhancements.

### 0.2.2 (2011-10-16)

  * Documentation enhancements.

### 0.2.1 (2011-10-15)

  * Add `PointInterface::move`.
  * `BoxInterface::scale` can accept floats.
  * Set antialias mode for GD images.
  * Fix png compression.

### 0.2.0 (2011-10-06)

  * Add `Imagine\Fill\Gradient\Linear::getStart`/`getEnd`.
  * Add `Imagine\Image\Color::isOpaque`.
  * Add Gmagick transparency exceptions.
  * Add support for transparency for gif export.
  * Add widen/heighten methods for easy scaling to target width/height.
  * Add functionals tests to unit test thumbnails creation.
  * Add the ability to use hexadecimal notation for `Imagine\Image\Color` construction.
  * Implement fast linear gradient for Imagick.
  * Remove lengthy image histogram comparisons.
  * Extract `ManipulatorInterface` from `ImageInterface`.
  * Switch methods to final.
  * New method `ImageInterface::getColorAt`.
  * Introduce `ImagineAware` abstract filter class.

### 0.1.5 (2011-05-18)

  * Fix bug in GD rotate.

### 0.1.4 (2011-03-21)

  * Add environment check to gracefuly skip test.

### 0.1.3 (2011-03-21)

  * Improve api docs.
  * Extract `FontInterface`.

### 0.1.2 (2011-03-21)

  * Add check for GD.

### 0.1.1 (2011-03-21)

  * Add rounding and fixed thumbnail logic.

### 0.1.0 (2011-03-14)

  * First tagged version.
