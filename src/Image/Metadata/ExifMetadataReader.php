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
        if (!self::isSupported()) {
            throw new NotSupportedException('PHP exif extension is required to use the ExifMetadataReader');
        }
    }

    /**
     * Is this metadata reader supported?
     *
     * @return bool
     */
    public static function isSupported()
    {
        return function_exists('exif_read_data');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Metadata\AbstractMetadataReader::extractFromFile()
     */
    protected function extractFromFile($file)
    {
        $loader = $file instanceof LoaderInterface ? $file : new Loader($file);

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

        return $this->extract('data://' . $mime . ';base64,' . base64_encode($data));
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
