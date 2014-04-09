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
 * Default metadata reader
 */
class DefaultMetadataReader implements MetadataReaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function readFile($file)
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException(sprintf('File %s does not exist.', $file));
        }

        return new MetadataBag(array('filepath' => realpath($file)));
    }

    /**
     * {@inheritdoc}
     */
    public function readData($data)
    {
        return new MetadataBag();
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Invalid resource provided.');
        }

        return new MetadataBag();
    }
}
