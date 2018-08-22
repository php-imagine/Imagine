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

use Imagine\Factory\ClassFactoryAwareInterface;
use Imagine\Factory\ClassFactoryInterface;

abstract class AbstractLayers implements LayersInterface, ClassFactoryAwareInterface
{
    /**
     * @var \Imagine\Factory\ClassFactoryInterface|null
     */
    private $classFactory;

    /**
     * {@inheritdoc}
     */
    public function add(ImageInterface $image)
    {
        $this[] = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function set($offset, ImageInterface $image)
    {
        $this[$offset] = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($offset)
    {
        unset($this[$offset]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($offset)
    {
        return $this[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function has($offset)
    {
        return isset($this[$offset]);
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
}
