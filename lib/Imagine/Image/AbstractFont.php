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

use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Factory\ClassFactoryAwareInterface;
use Imagine\Factory\ClassFactoryInterface;
use Imagine\Factory\ClassFactory;

/**
 * Abstract font base class.
 */
abstract class AbstractFont implements FontInterface, ClassFactoryAwareInterface
{
    /**
     * @var \Imagine\Factory\ClassFactoryInterface|null
     */
    private $classFactory;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var ColorInterface
     */
    protected $color;

    /**
     * Constructs a font with specified $file, $size and $color.
     *
     * The font size is to be specified in points (e.g. 10pt means 10)
     *
     * @param string $file
     * @param int $size
     * @param ColorInterface $color
     */
    public function __construct($file, $size, ColorInterface $color)
    {
        $this->file = $file;
        $this->size = $size;
        $this->color = $color;
    }

    /**
     * {@inheritdoc}
     */
    final public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    final public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    final public function getColor()
    {
        return $this->color;
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
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryAwareInterface::setClassFactory()
     */
    public function setClassFactory(ClassFactoryInterface $classFactory)
    {
        $this->classFactory = $classFactory;
        
        return $this;
    }
}
