<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

use Imagine\Effects\EffectsInterface;
use Imagine\Exception\RuntimeException;

class Effects implements EffectsInterface
{
    private $ressource;

    public function __construct($ressource)
    {
        $this->ressource = $ressource;
    }

    /**
     * {@inheritdoc}
     */
    public function gamma($correction)
    {
        if (false === imagegammacorrect($this->ressource, 1.0, $correction)) {
            throw new RuntimeException('Failed to apply gamma correction to the image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function negative()
    {
        if (false === imagefilter($this->ressource, IMG_FILTER_NEGATE)) {
           throw new RuntimeException('Failed to negate the image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function grayscale()
    {
        if (false === imagefilter($this->ressource, IMG_FILTER_GRAYSCALE)) {
           throw new RuntimeException('Failed to grayscale the image');
        }

        return $this;
    }
}
