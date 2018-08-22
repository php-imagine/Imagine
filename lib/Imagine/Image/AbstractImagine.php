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

use Imagine\Image\Metadata\MetadataReaderInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Factory\ClassFactoryInterface;

abstract class AbstractImagine implements ImagineInterface
{
    /** @var MetadataReaderInterface */
    private $metadataReader;

    /**
     * @var \Imagine\Factory\ClassFactoryInterface|null
     */
    private $classFactory;

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
            $this->metadataReader = $this->getClassFactory()->createMetadataReader();
        }

        return $this->metadataReader;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryAwareInterface::setClassFactory()
     */
    public function setClassFactory(ClassFactoryInterface $classFactory)
    {
        $this->classFactory = $classFactory;

        return $this;
    }
    
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryAwareInterface::getClassFactory()
     */
    public function getClassFactory()
    {
        if ($this->classFactory === null) {
            $this->classFactory = $this->createDefaultClassFactory();
        }

        return $this->classFactory;
    }

    /**
     * Create an instance of the default class factory.
     *
     * @return \Imagine\Factory\ClassFactoryInterface
     */
    protected abstract function createDefaultClassFactory();

    /**
     * Checks a path that could be used with ImagineInterface::open and returns
     * a proper string.
     *
     * @param string|object $path
     *
     * @return string
     *
     * @throws InvalidArgumentException In case the given path is invalid.
     */
    protected function checkPath($path)
    {
        // provide compatibility with objects such as \SplFileInfo
        if (is_object($path) && method_exists($path, '__toString')) {
            $path = (string) $path;
        }

        $handle = @fopen($path, 'r');

        if (false === $handle) {
            throw new InvalidArgumentException(sprintf('File %s does not exist.', $path));
        }

        fclose($handle);

        return $path;
    }
}
