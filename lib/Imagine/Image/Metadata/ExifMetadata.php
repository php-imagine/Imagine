<?php
namespace Imagine\Image\Metadata;

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Imagine\Image\ImageInterface;

/**
 * Metadata driven by Exif information
 */
class ExifMetadata implements MetadataInterface
{
    /**
     * @var ImageInterface
     */
    protected $image;

    /**
     * @var array
     */
    protected $exifData;

    /**
     * {@inheritdoc}
     */
    public function __construct(ImageInterface $image)
    {
        $this->image = $image;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrientation()
    {
        if ($this->exifData === null) {
            $this->initialize();
        }
        if (isset($this->exifData['Orientation'])) {
            return $this->exifData['Orientation'];
        }
        return null;
    }

    /**
     * Prepares the EXIF data, used for lazy-loading.
     * Should be called at first of any getter operation to make sure the exifData array is filled.
     */
    protected function initialize()
    {
        $exifData = exif_read_data('data://image/jpeg;base64,' . base64_encode($this->image->get('jpg')));
        if ($exifData !== false) {
            $this->exifData = $exifData;
        }
    }

}
