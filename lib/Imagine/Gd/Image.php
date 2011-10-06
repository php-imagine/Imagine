<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

use Imagine\Image\ImageInterface;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\Fill\FillInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Imagine\Image\Point\Center;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;
use Imagine\Gd\Imagine;
use Imagine\Mask\MaskInterface;

final class Image implements ImageInterface
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * Constructs a new Image instance using the result of
     * imagecreatetruecolor()
     *
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Makes sure the current image resource is destroyed
     */
    public function __destruct()
    {
        imagedestroy($this->resource);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::copy()
     */
    final public function copy()
    {
        $size = $this->getSize();

        $copy = $this->createImage($size, 'copy');

        if (false === imagecopy($copy, $this->resource, 0, 0, 0,
            0, $size->getWidth(), $size->getHeight())) {
            throw new RuntimeException('Image copy operation failed');
        }

        return new Image($copy);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::crop()
     */
    final public function crop(PointInterface $start, BoxInterface $size)
    {
        if (!$start->in($this->getSize())) {
            throw new OutOfBoundsException(
                'Crop coordinates must start at minimum 0, 0 position from '.
                'top  left corner, crop height and width must be positive '.
                'integers and must not exceed the current image borders'
            );
        }

        $width  = $size->getWidth();
        $height = $size->getHeight();

        $dest = $this->createImage($size, 'crop');

        if (false === imagecopy($dest, $this->resource, 0, 0,
            $start->getX(), $start->getY(), $width, $height)) {
            throw new RuntimeException('Image crop operation failed');
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::paste()
     */
    final public function paste(ImageInterface $image, PointInterface $start)
    {
        if (!$image instanceof self) {
            throw new InvalidArgumentException(sprintf(
                'Gd\Image can only paste() Gd\Image instances, %s given',
                get_class($image)
            ));
        }

        $size = $image->getSize();
        if (!$this->getSize()->contains($size, $start)) {
            throw new OutOfBoundsException(
                'Cannot paste image of the given size at the specified '.
                'position, as it moves outside of the current image\'s box'
            );
        }

        imagealphablending($this->resource, true);
        imagealphablending($image->resource, true);

        if (false === imagecopy($this->resource, $image->resource, $start->getX(), $start->getY(),
            0, 0, $size->getWidth(), $size->getHeight())) {
            throw new RuntimeException('Image paste operation failed');
        }

        imagealphablending($this->resource, false);
        imagealphablending($image->resource, false);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::resize()
     */
    final public function resize(BoxInterface $size)
    {
        $width  = $size->getWidth();
        $height = $size->getHeight();

        $dest = $this->createImage($size, 'resize');

        if (false === imagecopyresampled($dest, $this->resource, 0, 0, 0, 0,
            $width, $height, imagesx($this->resource), imagesy($this->resource)
        )) {
            throw new RuntimeException('Image resize operation failed');
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::rotate()
     */
    final public function rotate($angle, Color $background = null)
    {
        $color = $background ? $background : new Color('fff');

        $resource = imagerotate($this->resource, $angle, $this->getColor($color));

        if (false === $resource) {
            throw new RuntimeException('Image rotate operation failed');
        }

        imagedestroy($this->resource);

        $this->resource = $resource;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::save()
     */
    final public function save($path, array $options = array())
    {
        $format = isset($options['format'])
            ? $options['format']
            : pathinfo($path, \PATHINFO_EXTENSION);

        $this->saveOrOutput($format, $options, $path);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::show()
     */
    public function show($format, array $options = array())
    {
        $this->saveOrOutput($format, $options);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::get()
     */
    public function get($format, array $options = array())
    {
        ob_start();
        $this->saveOrOutput($format, $options);
        return ob_get_clean();
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::__toString()
     */
    public function __toString()
    {
        return $this->get('png');
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::flipHorizontally()
     */
    final public function flipHorizontally()
    {
        $size   = $this->getSize();
        $width  = $size->getWidth();
        $height = $size->getHeight();
        $dest   = $this->createImage($size, 'flip');

        for ($i = 0; $i < $width; $i++) {
            if (false === imagecopy($dest, $this->resource, $i, 0,
                ($width - 1) - $i, 0, 1, $height)) {
                throw new RuntimeException('Horizontal flip operation failed');
            }
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::flipVertically()
     */
    final public function flipVertically()
    {
        $size   = $this->getSize();
        $width  = $size->getWidth();
        $height = $size->getHeight();
        $dest   = $this->createImage($size, 'flip');

        for ($i = 0; $i < $height; $i++) {
            if (false === imagecopy($dest, $this->resource, 0, $i,
                0, ($height - 1) - $i, $width, 1)) {
                throw new RuntimeException('Vertical flip operation failed');
            }
        }

        imagedestroy($this->resource);

        $this->resource = $dest;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::thumbnail()
     */
    public function thumbnail(BoxInterface $size, $mode = ImageInterface::THUMBNAIL_INSET)
    {
        if ($mode !== ImageInterface::THUMBNAIL_INSET &&
            $mode !== ImageInterface::THUMBNAIL_OUTBOUND) {
            throw new InvalidArgumentException('Invalid mode specified');
        }

        $width  = $size->getWidth();
        $height = $size->getHeight();

        $ratios = array(
            $width / imagesx($this->resource),
            $height / imagesy($this->resource)
        );

        $thumbnail = $this->copy();

        if ($mode === ImageInterface::THUMBNAIL_INSET) {
            $ratio = min($ratios);
        } else if ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
            $ratio = max($ratios);
        }

        $thumbnailSize = $thumbnail->getSize()->scale($ratio);
        $thumbnail->resize($thumbnailSize);

        if ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
            $thumbnail->crop(new Point(
                max(0, round(($thumbnailSize->getWidth() - $width) / 2)),
                max(0, round(($thumbnailSize->getHeight() - $height) / 2))
            ), $size);
        }

        return $thumbnail;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::draw()
     */
    public function draw()
    {
        return new Drawer($this->resource);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::getSize()
     */
    public function getSize()
    {
        return new Box(imagesx($this->resource), imagesy($this->resource));
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::applyMask()
     */
    public function applyMask(ImageInterface $mask)
    {
        if (!$mask instanceof self) {
            throw new InvalidArgumentException('Cannot mask non-gd images');
        }

        $size     = $this->getSize();
        $maskSize = $mask->getSize();

        if ($size != $maskSize) {
            throw new InvalidArgumentException(sprintf(
                'The given mask doesn\'t match current image\'s size, Current '.
                'mask\'s dimensions are %s, while image\'s dimensions are %s',
                $maskSize, $size
            ));
        }

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x++) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y++) {
                $color     = imagecolorat($this->resource, $x, $y);
                $info      = imagecolorsforindex($this->resource, $color);
                $maskColor = $color = imagecolorat($mask->resource, $x, $y);
                $maskInfo  = imagecolorsforindex($mask->resource, $maskColor);
                if (false === imagesetpixel(
                    $this->resource,
                    $x, $y,
                    imagecolorallocatealpha(
                        $this->resource,
                        $info['red'],
                        $info['green'],
                        $info['blue'],
                        round((127 - $info['alpha']) * $maskInfo['red'] / 255)
                    )
                )) {
                    throw new RuntimeException('Apply mask operation failed');
                }
            }
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ManipulatorInterface::fill()
     */
    public function fill(FillInterface $fill)
    {
        $size = $this->getSize();

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x++) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y++) {
                if (false === imagesetpixel(
                    $this->resource,
                    $x, $y,
                    $this->getColor($fill->getColor(new Point($x, $y))))
                ) {
                    throw new RuntimeException('Fill operation failed');
                }
            }
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::mask()
     */
    public function mask()
    {
        $mask = $this->copy();

        if (false === imagefilter($mask->resource, IMG_FILTER_GRAYSCALE)) {
            throw new RuntimeException('Mask operation failed');
        }

        return $mask;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::histogram()
     */
    public function histogram()
    {
        $size   = $this->getSize();
        $colors = array();

        for ($x = 0, $width = $size->getWidth(); $x < $width; $x++) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; $y++) {
                $colors[] = $this->getColorAt(new Point($x, $y));
            }
        }

        return array_unique($colors);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\ImageInterface::getColorAt()
     */
    public function getColorAt(PointInterface $point) {
        if(!$point->in($this->getSize())) {
            throw new RuntimeException(sprintf(
                'Error getting color at point [%s,%s]. The point must be inside the image of size [%s,%s]',
                $point->getX(), $point->getY(), $this->getSize()->getWidth(), $this->getSize()->getHeight()
            ));
        }
        $index = imagecolorat($this->resource, $point->getX(), $point->getY());
        $info  = imagecolorsforindex($this->resource, $index);
        return new Color(array(
                $info['red'],
                $info['green'],
                $info['blue'],
            ),
            (int) round($info['alpha'] / 127 * 100)
        );
    }

    /**
     * Internal
     *
     * Performs save or show operation using one of GD's image... functions
     *
     * @param string $format
     * @param array  $options
     * @param string $filename
     * @param Imagine\Image\Color $color
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    private function saveOrOutput($format, array $options, $filename = null, Color $background = null)
    {

        if (!$this->supported($format)) {
            throw new InvalidArgumentException(sprintf(
                'Saving image in "%s" format is not supported, please use one '.
                'of the following extension: "%s"', $format,
                implode('", "', $this->supported())
            ));
        }

        $save = 'image'.$format;
        $args = array(&$this->resource, $filename);

        if (($format === 'jpeg' || $format === 'png') &&
            isset($options['quality'])) {
            // png compression quality is 0-9, so here we get the value from percent
            if ($format === 'png') {
                $options['quality'] = round($options['quality'] * 9 / 100);
            }
            $args[] = $options['quality'];
        }

        if ($format === 'jpeg') {
            $size   = $this->getSize();
            $output = $this->createImage($size, 'save jpeg');
            $color  = $this->getColor($background ? $background : new Color('fff'));

            imagefill($output, 0, 0, $color);
            imagealphablending($output, true);
            imagecopy($output, $this->resource, 0, 0, 0, 0, $size->getWidth(), $size->getHeight());
            imagealphablending($output, false);

            $this->resource = $output;
        }

        if ($format === 'png') {
            imagealphablending($this->resource, false);
            imagesavealpha($this->resource, true);

            if (isset($options['filters'])) {
                $args[] = $options['filters'];
            }
        }

        /*
         * Very heavy treatment, but obligate to transform each pixels
         */
        if ($format === 'gif'){
            $size       = $this->getSize();
            $output     = $this->createImage($size, 'save jpeg');
            $color      = $background ? $background : new Color('ffffff');
            $lightalpha = $strongalpha = false;

            for ($x = 0, $width = $size->getWidth(); $x < $width; $x++) {
                for ($y = 0, $height = $size->getHeight(); $y < $height; $y++) {
                    $rgb = imagecolorat($this->resource, $x, $y);
                    $colorAt = imagecolorsforindex($this->resource, $rgb);
                    // 100 because resize with copyresampled dissolve colors,
                    // normaly 1 for gif to gif, but as before ending, the output format isn't known...
                    if ($colorAt['alpha'] >= 100) {
                        imagesetpixel($this->resource, $x, $y, $this->getColor($color));
                        $strongalpha = true;
                    } elseif ($colorAt['alpha'] > 0) {
                        $lightalpha = true;
                    }
                }
            }

            if ($lightalpha) { //set a background
                imagefill($output, 0, 0, $this->getColor($color));
                imagealphablending($output, true);
                imagecopy($output, $this->resource, 0, 0, 0, 0, $size->getWidth(), $size->getHeight());
                imagealphablending($output, false);
                $this->resource = $output;
            }

            if ($strongalpha) { //set a transparency
                imagecolortransparent($this->resource, $this->getColor($color));
            }

        }

        if (($format === 'wbmp' || $format === 'xbm') &&
            isset($options['foreground'])) {
            $args[] = $options['foreground'];
        }

        if (false === call_user_func_array($save, $args)) {
            throw new RuntimeException('Save operation failed');
        }
    }

    /**
     * Internal
     *
     * Generates a GD image
     *
     * @param  Imagine\Image\BoxInterface $size
     * @param  string the operation initiating the creation
     *
     * @return resource
     *
     * @throws RuntimeException
     *
     */
    private function createImage(BoxInterface $size, $operation)
    {
        $resource = imagecreatetruecolor($size->getWidth(), $size->getHeight());

        if (false === $resource) {
            throw new RuntimeException('Image '.$operation.' failed');
        }

        if (false === imagealphablending($resource, false) ||
            false === imagesavealpha($resource, true)) {
            throw new RuntimeException('Image '.$operation.' failed');
        }

        if (function_exists('imageantialias')) {
            imageantialias($resource, true);
        }

        $red = $green = $blue = 255;

        $index = imagecolortransparent($this->resource);

        if($index !== -1){
            $color = imagecolorsforindex($this->resource, $index);
            $red   = $color['red'];
            $green = $color['green'];
            $blue  = $color['blue'];
        }

        imagefill($resource, 0, 0, imagecolorallocatealpha($resource, $red, $green, $blue, 127));

        return $resource;
    }

    /**
     * Internal
     *
     * Generates a GD color from Color instance
     *
     * @param  Imagine\Image\Color $color
     *
     * @return resource
     *
     * @throws Imagine\Exception\RuntimeException
     */
    private function getColor(Color $color)
    {
        static $cache = array();

        $key = (string) $color . "-" . $color->getAlpha();

        if (!isset($cache[$key])) {
            $cache[$key] = imagecolorallocatealpha(
                $this->resource, $color->getRed(), $color->getGreen(),
                $color->getBlue(), round(127 * $color->getAlpha() / 100)
            );
        }

        if (false === $cache[$key]) {
            throw new RuntimeException(sprintf(
                'Unable to allocate color "RGB(%s, %s, %s)" with transparency '.
                'of %d percent', $color->getRed(), $color->getGreen(),
                $color->getBlue(), $color->getAlpha()
            ));
        }

        return $cache[$key];
    }

    /**
     * Internal
     *
     * Checks whether a given format is supported by GD library
     *
     * @param string $format
     *
     * @return Boolean
     */
    private function supported(&$format = null)
    {
        $formats = array('gif', 'jpeg', 'png', 'wbmp', 'xbm');

        if (null === $format) {
            return $formats;
        }

        $format  = strtolower($format);

        if ('jpg' === $format || 'pjpeg' === $format) {
            $format = 'jpeg';
        }

        return in_array($format, $formats);
    }
}
