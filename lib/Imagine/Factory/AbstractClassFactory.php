<?php

namespace Imagine\Factory;

use Imagine\Image\Metadata\ExifMetadataReader;
use Imagine\Image\Metadata\DefaultMetadataReader;
use Imagine\File\Loader;
use Imagine\Image\Box;

/**
 * The default base implementation of Imagine\Factory\ClassFactoryInterface
 */
abstract class AbstractClassFactory implements ClassFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryInterface::createMetadataReader()
     */
    public function createMetadataReader()
    {
        return $this->finalize(ExifMetadataReader::isSupported() ? new ExifMetadataReader() : new DefaultMetadataReader());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryInterface::createBox()
     */
    public function createBox($width, $height)
    {
        return new Box($width, $height);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryInterface::createFileLoader()
     */
    public function createFileLoader($path)
    {
        return $this->finalize(new Loader($path));
    }

    /**
     * Finalize the newly created object.
     *
     * @param object $object
     *
     * @return object
     */
    protected function finalize($object)
    {
        if ($object instanceof ClassFactoryAwareInterface) {
            $object->setClassFactory($this);
        }

        return $object;
    }
}
