<?php

namespace Imagine\Factory;

/**
 * An interface that classes that accepts a class factory should implement.
 */
interface ClassFactoryAwareInterface
{
    /**
     * Set the class factory instance to be used.
     *
     * @param \Imagine\Factory\ClassFactoryInterface $classFactory
     *
     * @return $this
     */
    public function setClassFactory(ClassFactoryInterface $classFactory);

    /**
     * Get the class factory instance to be used.
     *
     * @return \Imagine\Factory\ClassFactoryInterface
     */
    public function getClassFactory();
}
