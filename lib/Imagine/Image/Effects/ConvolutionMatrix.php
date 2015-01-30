<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Amri Sannang <amri.sannang@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image\Effects;

use InvalidArgumentException;

/**
 * The convolution kernel class
 */
class ConvolutionMatrix implements ConvolutionMatrixInterface
{
    /**
     * The convolution kernel
     *
     * @var array|float[]
     */
    protected $kernel;

    /**
     * Constructor
     *
     * @param float $topLeft
     * @param float $topCenter
     * @param float $topRight
     * @param float $centerLeft
     * @param float $center
     * @param float $centerRight
     * @param float $bottomLeft
     * @param float $bottomCenter
     * @param float $bottomRight
     */
    public function __construct(
        $topLeft,
        $topCenter,
        $topRight,
        $centerLeft,
        $center,
        $centerRight,
        $bottomLeft,
        $bottomCenter,
        $bottomRight
    ) {
        $kernel = func_get_args();
        // Test types are all floats
        foreach ($kernel as $i => $val) {
            if (!is_numeric($val)) {
                throw new InvalidArgumentException('All values must be numeric');
            }
        }
        $this->setKernel($kernel);
    }

    /**
     * {@inheritdoc}
     */
    public function getDivisor()
    {
        // Assume that divisor should never be less than 1?
        return max(1, array_sum($this->getKernel()));
    }

    /**
     * Returns a copy, leaving the original kernel untouched.
     *
     * {@inheritdoc}
     */
    public function normalize()
    {
        $normalizedMatrix = array();
        $divisor          = $this->getDivisor();
        foreach ($this->getKernel() as $val) {
            $normalizedMatrix[] = $val / $divisor;
        }
        return call_user_func(get_class($this), $this->getKernel());
    }

    /**
     * @return array|array[]
     */
    public function getMatrix()
    {
        return array_chunk($this->kernel, 3);
    }

    /**
     * @return array|float[]
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @param  array|float[] $kernel
     *
     * @return $this
     */
    protected function setKernel($kernel)
    {
        $this->kernel = $kernel;
        return $this;
    }
}
