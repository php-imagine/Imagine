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
use Imagine\Factory\ClassFactoryAwareInterface;
use Imagine\Factory\ClassFactoryInterface;
use Imagine\Image\Palette\Color\ColorInterface;

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
     * @var \Imagine\Image\Palette\Color\ColorInterface
     */
    protected $color;

    /**
     * Constructs a font with specified $file, $size and $color.
     *
     * The font size is to be specified in points (e.g. 10pt means 10)
     *
     * @param string $file
     * @param int $size
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     */
    public function __construct($file, $size, ColorInterface $color)
    {
        $this->file = $file;
        $this->size = $size;
        $this->color = $color;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\FontInterface::getFile()
     */
    final public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\FontInterface::getSize()
     */
    final public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\FontInterface::getColor()
     */
    final public function getColor()
    {
        return $this->color;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\FontInterface::wrapText()
     */
    public function wrapText($string, $maxWidth, $angle = 0)
    {
        $string = (string) $string;
        if ($string === '') {
            return $string;
        }
        $maxWidth = (int) round($maxWidth);
        if ($maxWidth < 1) {
            throw new InvalidArgumentException(sprintf('The $maxWidth parameter of wrapText must be greater than 0.'));
        }
        $words = explode(' ', $string);
        $lines = array();
        $currentLine = null;
        foreach ($words as $word) {
            if ($currentLine === null) {
                $currentLine = $word;
            } else {
                $testLine = $currentLine . ' ' . $word;
                $testbox = $this->box($testLine, $angle);
                if ($testbox->getWidth() <= $maxWidth) {
                    $currentLine = $testLine;
                } else {
                    $lines[] = $currentLine;
                    $currentLine = $word;
                }
            }
        }
        if ($currentLine !== null) {
            $lines[] = $currentLine;
        }

        return implode("\n", $lines);
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
