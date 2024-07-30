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
use Imagine\File\Loader;
use Imagine\File\LoaderInterface;

/**
 * Base class for the default metadata readers.
 */
abstract class AbstractMetadataReader implements MetadataReaderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Metadata\MetadataReaderInterface::readFile()
     */
    public function readFile($file)
    {
        $loader = $file instanceof LoaderInterface ? $file : new Loader($file);

        return new MetadataBag(array_merge($this->getStreamMetadata($loader), $this->extractFromFile($loader)));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Metadata\MetadataReaderInterface::readData()
     */
    public function readData($data, $originalResource = null)
    {
        if ($originalResource !== null) {
            return new MetadataBag(array_merge($this->getStreamMetadata($originalResource), $this->extractFromData($data)));
        }

        return new MetadataBag($this->extractFromData($data));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\Metadata\MetadataReaderInterface::readStream()
     */
    public function readStream($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Invalid resource provided.');
        }

        return new MetadataBag(array_merge($this->getStreamMetadata($resource), $this->extractFromStream($resource)));
    }

    /**
     * Gets the URI from a stream resource.
     *
     * @param resource|\Imagine\File\LoaderInterface $resource
     *
     * @return array
     */
    private function getStreamMetadata($resource)
    {
        $metadata = array();

        if ($resource instanceof LoaderInterface) {
            $metadata['uri'] = $resource->getPath();
            if ($resource->isLocalFile()) {
                $metadata['filepath'] = realpath($resource->getPath());
            }
        } elseif (false !== $data = @stream_get_meta_data($resource)) {
            if (isset($data['uri'])) {
                $metadata['uri'] = $data['uri'];
                if (stream_is_local($resource)) {
                    $metadata['filepath'] = realpath($data['uri']);
                }
            }
        }

        return $metadata;
    }

    /**
     * Extracts metadata from a file.
     *
     * @param string|\Imagine\File\LoaderInterface $file
     *
     * @return array An associative array of metadata
     */
    abstract protected function extractFromFile($file);

    /**
     * Extracts metadata from raw data.
     *
     * @param string $data
     *
     * @return array An associative array of metadata
     */
    abstract protected function extractFromData($data);

    /**
     * Extracts metadata from a stream.
     *
     * @param resource $resource
     *
     * @return array An associative array of metadata
     */
    abstract protected function extractFromStream($resource);
}
