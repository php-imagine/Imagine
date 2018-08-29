<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

use Imagine\Image\Palette\PaletteInterface;

/**
 * The image interface.
 */
interface ImageInterface extends ManipulatorInterface
{
    /**
     * Resolution units: pixels per inch.
     *
     * @var string
     */
    const RESOLUTION_PIXELSPERINCH = 'ppi';

    /**
     * Resolution units: pixels per centimeter.
     *
     * @var string
     */
    const RESOLUTION_PIXELSPERCENTIMETER = 'ppc';

    /**
     * Image interlacing: none.
     *
     * @var string
     */
    const INTERLACE_NONE = 'none';

    /**
     * Image interlacing: scanline.
     *
     * @var string
     */
    const INTERLACE_LINE = 'line';

    /**
     * Image interlacing: plane.
     *
     * @var string
     */
    const INTERLACE_PLANE = 'plane';

    /**
     * Image interlacing: like plane interlacing except the different planes are saved to individual files.
     *
     * @var string
     */
    const INTERLACE_PARTITION = 'partition';

    /**
     * Image filter: none/undefined.
     *
     * @var string
     */
    const FILTER_UNDEFINED = 'undefined';

    /**
     * Resampling filter: point (interpolated).
     *
     * @var string
     */
    const FILTER_POINT = 'point';

    /**
     * Resampling filter: box.
     *
     * @var string
     */
    const FILTER_BOX = 'box';

    /**
     * Resampling filter: triangle.
     *
     * @var string
     */
    const FILTER_TRIANGLE = 'triangle';

    /**
     * Resampling filter: hermite.
     *
     * @var string
     */
    const FILTER_HERMITE = 'hermite';

    /**
     * Resampling filter: hanning.
     *
     * @var string
     */
    const FILTER_HANNING = 'hanning';

    /**
     * Resampling filter: hamming.
     *
     * @var string
     */
    const FILTER_HAMMING = 'hamming';

    /**
     * Resampling filter: blackman.
     *
     * @var string
     */
    const FILTER_BLACKMAN = 'blackman';

    /**
     * Resampling filter: gaussian.
     *
     * @var string
     */
    const FILTER_GAUSSIAN = 'gaussian';

    /**
     * Resampling filter: quadratic.
     *
     * @var string
     */
    const FILTER_QUADRATIC = 'quadratic';

    /**
     * Resampling filter: cubic.
     *
     * @var string
     */
    const FILTER_CUBIC = 'cubic';

    /**
     * Resampling filter: catrom.
     *
     * @var string
     */
    const FILTER_CATROM = 'catrom';

    /**
     * Resampling filter: mitchell.
     *
     * @var string
     */
    const FILTER_MITCHELL = 'mitchell';

    /**
     * Resampling filter: lanczos.
     *
     * @var string
     */
    const FILTER_LANCZOS = 'lanczos';

    /**
     * Resampling filter: bessel.
     *
     * @var string
     */
    const FILTER_BESSEL = 'bessel';

    /**
     * Resampling filter: sinc.
     *
     * @var string
     */
    const FILTER_SINC = 'sinc';

    /**
     * Returns the image content as a binary string.
     *
     * @param string $format
     * @param array $options
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return string binary
     */
    public function get($format, array $options = array());

    /**
     * Returns the image content as a PNG binary string.
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return string binary
     */
    public function __toString();

    /**
     * Instantiates and returns a DrawerInterface instance for image drawing.
     *
     * @return \Imagine\Draw\DrawerInterface
     */
    public function draw();

    /**
     * @return \Imagine\Effects\EffectsInterface
     */
    public function effects();

    /**
     * Returns current image size.
     *
     * @return \Imagine\Image\BoxInterface
     */
    public function getSize();

    /**
     * Transforms creates a grayscale mask from current image, returns a new
     * image, while keeping the existing image unmodified.
     *
     * @return static
     */
    public function mask();

    /**
     * Returns array of image colors as Imagine\Image\Palette\Color\ColorInterface instances.
     *
     * @return \Imagine\Image\Palette\Color\ColorInterface[]
     */
    public function histogram();

    /**
     * Returns color at specified positions of current image.
     *
     * @param \Imagine\Image\PointInterface $point
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return \Imagine\Image\Palette\Color\ColorInterface
     */
    public function getColorAt(PointInterface $point);

    /**
     * Returns the image layers when applicable.
     *
     * @throws \Imagine\Exception\RuntimeException In case the layer can not be returned
     * @throws \Imagine\Exception\OutOfBoundsException In case the index is not a valid value
     *
     * @return \Imagine\Image\LayersInterface
     */
    public function layers();

    /**
     * Enables or disables interlacing.
     *
     * @param string $scheme
     *
     * @throws \Imagine\Exception\InvalidArgumentException When an unsupported Interface type is supplied
     *
     * @return $this
     */
    public function interlace($scheme);

    /**
     * Return the current color palette.
     *
     * @return \Imagine\Image\Palette\PaletteInterface
     */
    public function palette();

    /**
     * Set a palette for the image. Useful to change colorspace.
     *
     * @param \Imagine\Image\Palette\PaletteInterface $palette
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function usePalette(PaletteInterface $palette);

    /**
     * Applies a color profile on the Image.
     *
     * @param \Imagine\Image\ProfileInterface $profile
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function profile(ProfileInterface $profile);

    /**
     * Returns the Image's meta data.
     *
     * @return \Imagine\Image\Metadata\MetadataBag
     */
    public function metadata();
}
