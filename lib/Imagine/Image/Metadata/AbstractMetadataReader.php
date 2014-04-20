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

abstract class AbstractMetadataReader implements MetadataReaderInterface
{
    /**
     * Gets the URI from a stream resource
     *
     * @param resource $resource
     *
     * @return string|null The URI f ava
     */
    protected function getStreamMetadata($resource)
    {
        $metadata = array();

        if (false !== $data = @stream_get_meta_data($resource)) {
            $metadata['uri'] = $data['uri'];
            if (stream_is_local($resource)) {
                $metadata['filepath'] = realpath($data['uri']);
            }
        }

        return $metadata;
    }
}
