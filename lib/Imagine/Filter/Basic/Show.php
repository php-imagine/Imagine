<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter\Basic;

use Imagine\ImageInterface;
use Imagine\Filter\FilterInterface;

class Show implements FilterInterface
{
    private $format;
    private $options;

    /**
     * Constructs the Show filter with given format and options
     *
     * @param string $format
     * @param array  $options
     */
    public function __construct($format, array $options = array())
    {
        $this->format  = $format;
        $this->options = $options;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        echo $image->get($this->format, $this->options);
        return $image;
    }
}
