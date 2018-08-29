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

/**
 * The default implementation of ProfileInterface.
 */
class Profile implements ProfileInterface
{
    /**
     * @var string
     */
    private $data;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     * @param string $data
     */
    public function __construct($name, $data)
    {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ProfileInterface::name()
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ProfileInterface::data()
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Creates a profile from a path to a file.
     *
     * @param string $path
     *
     * @throws \Imagine\Exception\InvalidArgumentException In case the provided path is not valid
     *
     * @return static
     */
    public static function fromPath($path)
    {
        if (!file_exists($path) || !is_file($path) || !is_readable($path)) {
            throw new InvalidArgumentException(sprintf('Path %s is an invalid profile file or is not readable', $path));
        }

        return new static(basename($path), file_get_contents($path));
    }
}
