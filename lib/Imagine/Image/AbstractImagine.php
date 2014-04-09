<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

use Imagine\Image\Metadata\DefaultMetadataReader;
use Imagine\Image\Metadata\MetadataReaderInterface;

abstract class AbstractImagine implements ImagineInterface
{
    /** @var MetadataReaderInterface */
    private $metadataReader;

    /**
     * @param MetadataReaderInterface $metadataReader
     *
     * @return ImagineInterface
     */
    public function setMetadataReader(MetadataReaderInterface $metadataReader)
    {
        $this->metadataReader = $metadataReader;

        return $this;
    }

    /**
     * @return MetadataReaderInterface
     */
    public function getMetadataReader()
    {
        if (null === $this->metadataReader) {
            $this->metadataReader = new DefaultMetadataReader();
        }

        return $this->metadataReader;
    }
}
