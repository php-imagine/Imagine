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

use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\NotSupportedException;

/**
 * Metadata driven by Exif information
 */
class ExifMetadataReader extends AbstractMetadataReader
{
    public function __construct()
    {
        if (!function_exists('exif_read_data')) {
            throw new NotSupportedException('PHP exif extension is required to use the ExifMetadataReader');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function readFile($file)
    {
        if (stream_is_local($file)) {
            if (!is_file($file)) {
                throw new InvalidArgumentException(sprintf('File %s does not exist.', $file));
            }

            return $this->extract($file, array('filepath' => realpath($file), 'uri' => $file));
        }

        if (false === $data = @file_get_contents($file)) {
            throw new InvalidArgumentException(sprintf('File %s is not readable.', $file));
        }

        return $this->doReadData($data, array('uri' => $file));
    }

    /**
     * {@inheritdoc}
     */
    public function readData($data)
    {
        return $this->doReadData($data, array());
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Invalid resource provided.');
        }

        $metadata = $this->getStreamMetadata($resource);

        return $this->doReadData(stream_get_contents($resource), $metadata);
    }

    /**
     * Extracts metadata from raw data, merges with existing metadata
     *
     * @param string $data
     * @param array  $metadata
     *
     * @return MetadataBag
     */
    private function doReadData($data, array $metadata)
    {
        if (substr($data, 0, 2) === 'II') {
            $mime = 'image/tiff';
        } else {
            $mime = 'image/jpeg';
        }

        return $this->extract('data://' . $mime . ';base64,' . base64_encode($data), $metadata);
    }

    /**
     * Performs the exif data extraction given a path or data-URI representation.
     *
     * @param string $path The path to the file or the data-URI representation.
     * @param array  $data An array of extra-metadata to consider
     *
     * @return MetadataBag
     */
    private function extract($path, array $data = array())
    {
        if (false === $exifData = @exif_read_data($path, null, true, false)) {
            return new MetadataBag($data);
        }

        $metadata = array();
        $sources = array('EXIF' => 'exif', 'IFD0' => 'ifd0');

        foreach ($sources as $name => $prefix) {
            if (!isset($exifData[$name])) {
                continue;
            }
            foreach ($exifData[$name] as $prop => $value) {
                $metadata[$prefix.'.'.$prop] = $value;
            }
        }

        return new MetadataBag(array_merge($data, $metadata));
    }
}
