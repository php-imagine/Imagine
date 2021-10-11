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

use Imagine\Image\Histogram\Bucket;
use Imagine\Image\Histogram\Range;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\PaletteInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Test\InvalidArgumentFactory;

class IsImageEqual extends Constraint
{
    /**
     * @var \Imagine\Image\ImagineInterface|null
     */
    private $imagine;

    /**
     * @var \Imagine\Image\ImageInterface
     */
    private $expectedImage;

    /**
     * @var string
     */
    private $expectedImageFile;

    /**
     * @var array
     */
    private $calculatedDeltas = array();

    /**
     * @var float
     */
    private $delta;

    /**
     * @var int
     */
    private $buckets;

    /**
     * @param \Imagine\Image\ImageInterface|string $expected
     * @param float $delta
     * @param \Imagine\Image\ImagineInterface|null $imagine
     * @param int $buckets
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($expected, $delta = 0.1, ImagineInterface $imagine = null, $buckets = 4)
    {
        parent::__construct();
        $this->imagine = $imagine;
        $this->expectedImageFile = is_string($expected) ? str_replace('/', DIRECTORY_SEPARATOR, $expected) : '';
        if ($this->imagine !== null && is_string($expected)) {
            $expected = $this->imagine->open($expected);
        }
        if (!($expected instanceof ImageInterface)) {
            throw InvalidArgumentFactory::create(1, 'Imagine\Image\ImageInterface');
        }

        if (!is_numeric($delta)) {
            throw InvalidArgumentFactory::create(2, 'numeric');
        }

        if (!is_int($buckets) || $buckets <= 0) {
            throw InvalidArgumentFactory::create(4, 'integer');
        }

        $this->expectedImage = $expected;
        $this->delta = $delta;
        $this->buckets = $buckets;
    }

    /**
     * {@inheritdoc}
     */
    protected function _matches($other)
    {
        $total = $this->getDelta($other);

        return $total <= $this->delta;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toString()
    {
        return sprintf('has a color histogram similar to the expected %s', $this->exporter->export($this->expectedImage));
    }

    /**
     * {@inheritdoc}
     */
    protected function _failureDescription($other)
    {
        $extraMessage = '';
        if (is_string($other) && IMAGINE_TEST_KEEP_TEMPFILES === true) {
            if ($this->expectedImageFile !== '') {
                $expectedPrefix = dirname(IMAGINE_TEST_FIXTURESFOLDER) . DIRECTORY_SEPARATOR;
                if (strlen($this->expectedImageFile) > strlen($expectedPrefix) && strpos($this->expectedImageFile, $expectedPrefix) === 0) {
                    $expectedBaseName = substr($this->expectedImageFile, strlen($expectedPrefix));
                } else {
                    $expectedBaseName = $this->expectedImageFile;
                }
                $extraMessage .= "\nExpected file: {$expectedBaseName}";
            }
            $otherPrefix = IMAGINE_TEST_TEMPFOLDER . DIRECTORY_SEPARATOR;
            $other = str_replace('/', DIRECTORY_SEPARATOR, $other);
            if (strlen($other) > strlen($otherPrefix) && strpos($other, $otherPrefix) === 0) {
                $otherBaseName = substr($other, strlen($otherPrefix));
            } else {
                $otherBaseName = $other;
            }
            $extraMessage .= "\nActual file: {$otherBaseName}";
        }

        return sprintf('has a color histogram similar to the expected one (max delta: %s, actual delta: %s)', $this->delta, $this->getDelta($other)) . $extraMessage;
    }

    /**
     * @param \Imagine\Image\ImageInterface $image
     *
     * @return array
     */
    private function normalize(ImageInterface $image)
    {
        if ($image->palette()->name() !== PaletteInterface::PALETTE_RGB) {
            $image = $image->copy()->usePalette(new RGB());
        }
        $step = (int) round(255 / $this->buckets);

        $red =
        $green =
        $blue =
        $alpha = array();

        for ($i = 1; $i <= $this->buckets; $i++) {
            $range = new Range(($i - 1) * $step, $i * $step);
            $red[] = new Bucket($range);
            $green[] = new Bucket($range);
            $blue[] = new Bucket($range);
            $alpha[] = new Bucket($range);
        }
        $colors = $image->histogram();
        foreach ($colors as $color) {
            foreach ($red as $bucket) {
                if ($color->getAlpha() !== 0) {
                    $bucket->add($color->getRed());
                }
            }

            foreach ($green as $bucket) {
                if ($color->getAlpha() !== 0) {
                    $bucket->add($color->getGreen());
                }
            }

            foreach ($blue as $bucket) {
                if ($color->getAlpha() !== 0) {
                    $bucket->add($color->getBlue());
                }
            }

            foreach ($alpha as $bucket) {
                $bucket->add(round($color->getAlpha() * 2.55));
            }
        }

        $total = $image->getSize()->square();

        $callback = function (Bucket $bucket) use ($total) {
            return count($bucket) / $total;
        };

        return array(
            array_map($callback, $red),
            array_map($callback, $green),
            array_map($callback, $blue),
            array_map($callback, $alpha),
        );
    }

    /**
     * @param \Imagine\Image\ImageInterface|string $other
     *
     * @return float
     */
    private function getDelta($other)
    {
        $otherKey = $this->getOtherKey($other);
        if (!array_key_exists($otherKey, $this->calculatedDeltas)) {
            if ($this->imagine !== null && is_string($other)) {
                $other = $this->imagine->open($other);
            }
            if (!$other instanceof ImageInterface) {
                throw InvalidArgumentFactory::create(1, 'Imagine\Image\ImageInterface');
            }
            $this->calculatedDeltas[$otherKey] = $this->calculateDelta($other);
        }

        return $this->calculatedDeltas[$otherKey];
    }

    /**
     * Get a string that uniquely identifies the "other" resource.
     *
     * @param \Imagine\Image\ImageInterface|string $other
     */
    private function getOtherKey($other)
    {
        return is_string($other) ? $other : spl_object_hash($other);
    }

    /**
     * @param \Imagine\Image\ImageInterface $other
     *
     * @return float
     */
    private function calculateDelta(ImageInterface $other)
    {
        list($currentRed, $currentGreen, $currentBlue, $currentAlpha) = $this->normalize($this->expectedImage);
        list($otherRed, $otherGreen, $otherBlue, $otherAlpha) = $this->normalize($other);
        $total = 0.0;
        foreach ($currentRed as $bucket => $count) {
            $total += abs($count - $otherRed[$bucket]);
        }
        foreach ($currentGreen as $bucket => $count) {
            $total += abs($count - $otherGreen[$bucket]);
        }
        foreach ($currentBlue as $bucket => $count) {
            $total += abs($count - $otherBlue[$bucket]);
        }
        foreach ($currentAlpha as $bucket => $count) {
            $total += abs($count - $otherAlpha[$bucket]);
        }

        return $total;
    }
}
