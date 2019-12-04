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

use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Test\InvalidArgumentFactory;

class IsColorSimilar extends Constraint
{
    /**
     * @var \Imagine\Image\Palette\Color\ColorInterface
     */
    private $value;

    /**
     * @var float
     */
    private $maxDistance;

    /**
     * @var bool
     */
    private $includeAlpha;

    /**
     * @var int
     */
    private $channelMultiplier;

    /**
     * @var string[]
     */
    private $pixelDefinition;

    /**
     * @param \Imagine\Image\Palette\Color\ColorInterface $value
     * @param float $maxDistance
     * @param bool $includeAlpha
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($value, $maxDistance = 0.1, $includeAlpha = true)
    {
        parent::__construct();
        if (!$value instanceof ColorInterface) {
            throw InvalidArgumentFactory::create(1, 'Imagine\Image\Palette\Color\ColorInterface', $value);
        }
        $this->value = $value;

        if (is_string($maxDistance) && is_numeric($maxDistance)) {
            $maxDistance = (float) $maxDistance;
        }
        if (is_int($maxDistance)) {
            $maxDistance = (float) $maxDistance;
        } elseif (!is_float($maxDistance)) {
            throw InvalidArgumentFactory::create(2, 'float', $maxDistance);
        }
        if ($maxDistance < 0) {
            throw InvalidArgumentFactory::create(2, 'float', $maxDistance);
        }
        $this->maxDistance = $maxDistance;
        $this->includeAlpha = (bool) $includeAlpha;
        $this->pixelDefinition = $this->value->getPalette()->pixelDefinition();
        $this->channelMultiplier = $this->value->getPalette()->getChannelsMaxValue();
    }

    /**
     * {@inheritdoc}
     */
    protected function _matches($other)
    {
        if (!$other instanceof ColorInterface) {
            throw InvalidArgumentFactory::create(1, 'Imagine\Image\Palette\Color\ColorInterface', $other);
        }
        $pixelDefinition = $other->getPalette()->pixelDefinition();
        if ($pixelDefinition !== $this->pixelDefinition) {
            throw InvalidArgumentFactory::create(1, 'Imagine\Image\Palette\Color\ColorInterface', $other);
        }

        $distance = $this->calculateDistance($other);

        return $distance <= $this->maxDistance;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toString()
    {
        return sprintf(
            'is a color with a maximum distance of %s from %s',
            $this->maxDistance,
            $this->stringify($this->value)
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param \Imagine\Image\Palette\Color\ColorInterface $other
     */
    protected function _failureDescription($other)
    {
        return sprintf(
            'the color %s has a distance from %s not greater than %s (actual distance: %s)',
            $this->stringify($other),
            $this->stringify($this->value),
            $this->maxDistance,
            $this->calculateDistance($other)
        );
    }

    /**
     * @param \Imagine\Image\Palette\Color\ColorInterface $other
     * @param ColorInterface $other
     *
     * @return float
     */
    protected function calculateDistance(ColorInterface $other)
    {
        $squareSum = 0.0;
        foreach ($this->pixelDefinition as $color) {
            $squareSum += pow($other->getValue($color) - $this->value->getValue($color), 2);
        }
        if ($this->includeAlpha) {
            $myAlpha = $this->value->getAlpha();
            if ($myAlpha === null) {
                $myAlpha = 100;
            }
            $otherAlpha = $other->getAlpha();
            if ($otherAlpha === null) {
                $otherAlpha = 100;
            }
            $squareSum += pow(($otherAlpha - $myAlpha) * $this->channelMultiplier / 100, 2);
        }

        return sqrt($squareSum);
    }

    /**
     * @param \Imagine\Image\Palette\Color\ColorInterface $other
     * @param ColorInterface $color
     *
     * @return string
     */
    protected function stringify(ColorInterface $color)
    {
        $result = $color->__toString();
        if ($this->includeAlpha) {
            $alpha = $color->getAlpha();
            if ($alpha === null) {
                $alpha = 100;
            }

            return sprintf('%s (alpha: %s)', $result, $alpha);
        }

        return $result;
    }
}
