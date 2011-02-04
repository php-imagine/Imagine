<?php

namespace Imagine\Filter\Advanced;

use Imagine\Color;
use Imagine\ImageInterface;
use Imagine\Filter\Advanced\Thumbnail\DownSizer;
use Imagine\Filter\Advanced\Thumbnail\StandardResizer;
use Imagine\Filter\Advanced\Thumbnail\UpSizer;
use Imagine\Filter\FilterInterface;
use Imagine\Gd\BlankImage;

class Thumbnail implements FilterInterface
{
    const UPSCALE_COLOR   = 0;
    const UPSCALE_RESIZE  = 1;
    const UPSCALE_STRETCH = 2;

    private $width;
    private $height;
    private $background;
    private $upscale;

    /**
     * Resizes image to the maximum possible size, constraining proportions
     * Fills image to the size of resized image is less that required size
     *
     * @param integer    $width
     * @param integer    $height
     * @param integer    $upscale
     * @param Color      $background
     */
    public function __construct($width, $height,
        $upscale = self::UPSCALE_COLOR, Color $background = null)
    {
        $this->width      = $width;
        $this->height     = $height;
        $this->upscale    = $upscale;
        $this->background = $background ? : new Color('fff');
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        if ($image->getWidth() < $this->width ||
            $image->getHeight() < $this->height) {

            switch ($this->upscale) {
                case self::UPSCALE_COLOR:
                    $temp   = $image;
                    $width  = $image->getWidth();
                    $height = $image->getHeight();
                    $x = $y = 0;

                    if ($image->getWidth() < $this->width) {
                        $width = $this->width;
                        $x = round(($this->width - $image->getWidth()) / 2);
                    }

                    if ($image->getHeight() < $this->height) {
                        $height = $this->height;
                        $y = round(($this->height - $image->getHeight()) / 2);
                    }

                    $image = new BlankImage($width, $height, $this->background);
                    $image->paste($temp, $x, $y);

                    unset($temp);
                    break;
                case self::UPSCALE_RESIZE:
                    $ratio = max(array(
                        $this->width / $image->getWidth(),
                        $this->height / $image->getHeight()
                    ));

                    $image->resize(
                        $image->getWidth() * $ratio,
                        $image->getHeight() * $ratio
                    );
                    break;
                case self::UPSCALE_STRETCH:
                    if ($image->getWidth() < $this->width) {
                        $image->resize($this->width, $image->getHeight());
                    }

                    if ($image->getHeight() < $this->height) {
                        $image->resize($image->getWidth(), $this->height);
                    }
                    break;
            }
        }

        $ratio = min(array(
            $image->getWidth() / $this->width,
            $image->getHeight() / $this->height
        ));

        $image->resize(
            $image->getWidth() / $ratio,
            $image->getHeight() / $ratio
        );

        if ($image->getWidth() > $this->width ||
            $image->getHeight() > $this->height) {

            $x = $y = 0;

            if ($image->getWidth() > $this->width) {
                $x = round(($image->getWidth() - $this->width) / 2);
            }

            if ($image->getHeight() > $this->height) {
                $y = round(($image->getHeight() - $this->height) / 2);
            }

            $image->crop($x, $y, $this->width, $this->height);
        }

        return $image;
    }
}
