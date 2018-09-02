<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Constraint;

use Imagine\Image\BoxInterface;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Util\InvalidArgumentHelper;

class IsBoxInRange extends Constraint
{
    /**
     * @var int
     */
    private $minWidth;

    /**
     * @var int
     */
    private $maxWidth;

    /**
     * @var int
     */
    private $minHeight;

    /**
     * @var int
     */
    private $maxHeight;

    public function __construct($minWidth, $maxWidth, $minHeight, $maxHeight)
    {
        parent::__construct();
        if (!is_int($minWidth) || $minWidth < 0) {
            throw InvalidArgumentHelper::factory(1, 'integer');
        }
        $this->minWidth = $minWidth;
        if (!is_int($maxWidth) || $maxWidth < $minWidth) {
            throw InvalidArgumentHelper::factory(2, 'integer');
        }
        $this->maxWidth = $maxWidth;
        if (!is_int($minHeight) || $minHeight < 0) {
            throw InvalidArgumentHelper::factory(3, 'integer');
        }
        $this->minHeight = $minHeight;
        if (!is_int($maxHeight) || $maxHeight < $minHeight) {
            throw InvalidArgumentHelper::factory(4, 'integer');
        }
        $this->maxHeight = $maxHeight;
    }

    /**
     * {@inheritdoc}
     */
    protected function matches($other)
    {
        if (!$other instanceof BoxInterface) {
            throw InvalidArgumentHelper::factory(1, 'Imagine\Image\BoxInterface');
        }

        return
            $this->minWidth <= $other->getWidth() && $other->getWidth() <= $this->maxWidth
            &&
            $this->minHeight <= $other->getHeight() && $other->getHeight() <= $this->maxHeight
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return sprintf(
            'is a box with a width between %d and %d, and a height between %d and %d',
            $this->minWidth, $this->maxWidth,
            $this->minHeight, $this->maxHeight
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param \Imagine\Image\BoxInterface $other
     */
    public function failureDescription($other)
    {
        return sprintf(
            'the box %sx%s has a width between %d and %d, and a height between %d and %d',
            $other->getWidth(), $other->getHeight(),
            $this->minWidth, $this->maxWidth,
            $this->minHeight, $this->maxHeight
        );
    }
}
