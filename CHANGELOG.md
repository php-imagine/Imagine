# CHANGELOG

### 0.5.0 (2013-xx-xx)

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
