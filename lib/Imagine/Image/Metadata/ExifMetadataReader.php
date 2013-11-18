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

use Imagine\Exception\InvalidArgumentException;

/**
 * Metadata driven by Exif information
 */
class ExifMetadataReader implements MetadataReaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function readFile($file)
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException(sprintf('File %s does not exist.', $file));
        }

        return $this->extract($file, array('filepath' => realpath($file)));
    }

    /**
     * {@inheritdoc}
     */
    public function readData($data)
    {
        if (substr($data, 0, 2) === 'II') {
            $mime = 'image/tiff';
        } else {
            $mime = 'image/jpeg';
        }

        return $this->extract('data://' . $mime . ';base64,' . base64_encode($data));
    }

    public function readStream($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Invalid resource provided.');
        }

        $data = stream_get_contents($resource);

        return $this->readData($data);
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
