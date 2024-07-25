<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image\Metadata;

use Imagine\Exception\NotSupportedException;
use Imagine\File\Loader;
use Imagine\File\LoaderInterface;
use Imagine\Utils\ErrorHandling;

/**
 * Metadata driven by Exif information.
 */
class ExifMetadataReader extends AbstractMetadataReader
{
    /**
     * @throws \Imagine\Exception\NotSupportedException
     */
    public function __construct()
    {
    }

    /**
     * Get the reason why this metadata reader is not supported.
     *
     * @return string empty string if the reader is available
     */
    public static function getUnsupportedReason()
    {
        if (!function_exists('exif_read_data')) {
            return 'The PHP EXIF extension is required to use the ExifMetadataReader';
        }
        if (!in_array('data', stream_get_wrappers(), true)) {
            return 'The data:// stream wrapper must be enabled';
        }
        if (in_array(ini_get('allow_url_fopen'), array('', '0', 0), true)) {
            return 'The allow_url_fopen php.ini configuration key must be set to 1';
        }

        return '';
    }

    /**
     * Is this metadata reader supported?
     *
     * @return bool
     */
    public static function isSupported()
    {
        return static::getUnsupportedReason() === '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Metadata\AbstractMetadataReader::extractFromFile()
     */
    protected function extractFromFile($file)
    {
        $loader = $file instanceof LoaderInterface ? $file : new Loader($file);

        if ($loader->isLocalFile()) {
            return $this->extract($loader->getPath());
        }

        return $this->doReadData($loader->getData());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Metadata\AbstractMetadataReader::extractFromData()
     */
    protected function extractFromData($data)
    {
        return $this->doReadData($data);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Metadata\AbstractMetadataReader::extractFromStream()
     */
    protected function extractFromStream($resource)
    {
        return $this->doReadData(stream_get_contents($resource));
    }

    /**
     * Extracts metadata from raw data, merges with existing metadata.
     *
     * @param string $data
     *
     * @return array
     */
    private function doReadData($data)
    {
        if (substr($data, 0, 2) === 'II') {
            $mime = 'image/tiff';
        } else {
            $mime = 'image/jpeg';
        }

        // Convert the image to a form that can be read by exif_read_data()
        // If allow_url_fopen is available do this using the data:// structure

        $whyNot = static::getUnsupportedReason();
        if ($whyNot === '') {
            // allow_url_fopen is available, so use default method
            $file_handle = $this->extract('data://' . $mime . ';base64,' . base64_encode($data));
            // Now see if we can extract anything ... 
            $exifData = $this->extract($file_handle);
        } elseif(substr($data, 0, 2) === 'II') {
            // Tiff image - cannot be written by GD so bale
            throw new NotSupportedException('Tiff file format not supported on GD2');
        } else {
            $file_handle = hash('tiger160,3',$data).'jpg';
            // Write file to temporary file on disk
            try {
                imagejpeg(imagecreatefromstring($data),$file_handle,0);
            } catch (\Exception $e) {
                // image creation failed so return nothing
                return array();
            }
            // Now see if we can extract anything ... 
            $exifData = $this->extract($file_handle);
            unset($file_handle);
        }
        return $exifData;
    }

    /**
     * Performs the exif data extraction given a path or data-URI representation.
     *
     * @param string $path the path to the file or the data-URI representation
     *
     * @return array
     */
    private function extract($path)
    {
        try {
            $exifData = ErrorHandling::ignoring(-1, function () use ($path) {
                return @exif_read_data($path, null, true, false);
            });
        } catch (\Exception $e) {
            $exifData = false;
        } catch (\Throwable $e) {
            $exifData = false;
        }
        if (!is_array($exifData)) {
            return array();
        }

        $metadata = array();
        foreach ($exifData as $prefix => $values) {
            if (is_array($values)) {
                $prefix = strtolower($prefix);
                foreach ($values as $prop => $value) {
                    $metadata[$prefix . '.' . $prop] = $value;
                }
            }
        }

        return $metadata;
    }
}
