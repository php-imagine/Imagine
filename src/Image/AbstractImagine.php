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

use Imagine\Exception\InvalidArgumentException;
use Imagine\Factory\ClassFactory;
use Imagine\Factory\ClassFactoryInterface;
use Imagine\Image\Metadata\MetadataReaderInterface;

abstract class AbstractImagine implements ImagineInterface
{
    /**
     * @var \Imagine\Image\Metadata\MetadataReaderInterface|null
     */
    private $metadataReader;

    /**
     * @var \Imagine\Factory\ClassFactoryInterface
     */
    private $classFactory;

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImagineInterface::setMetadataReader()
     */
    public function setMetadataReader(MetadataReaderInterface $metadataReader)
    {
        $this->metadataReader = $metadataReader;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImagineInterface::getMetadataReader()
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
            $this->classFactory = new ClassFactory();
        }

        return $this->classFactory;
    }

    /**
     * Checks a path that could be used with ImagineInterface::open and returns
     * a proper string.
     *
     * @param string|object $path
     *
     * @throws \Imagine\Exception\InvalidArgumentException in case the given path is invalid
     *
     * @return string
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
