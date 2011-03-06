<?php

namespace Imagine\Test\Constraint;

use Imagine\Image\Histogram\Range;

use Imagine\Image\Histogram\Bucket;

use Imagine\ImageInterface;

class IsImageEqual extends \PHPUnit_Framework_Constraint
{
    /**
     * @var Imagine\ImageInterface
     */
    private $value;

    /**
     * @var float
     */
    private $delta;

    /**
     * @var integer
     */
    private $buckets;

    public function __construct($value, $delta = 1, $buckets = 4)
    {
        if (!$value instanceof ImageInterface) {
            throw \PHPUnit_Util_InvalidArgumentHelper::factory(1, 'Imagine\ImageInterface');
        }

        if (!is_numeric($delta)) {
            throw \PHPUnit_Util_InvalidArgumentHelper::factory(2, 'numeric');
        }

        if (!is_integer($buckets) || $buckets <= 0) {
            throw \PHPUnit_Util_InvalidArgumentHelper::factory(3, 'integer');
        }

        $this->value   = $value;
        $this->delta   = $delta;
        $this->buckets = $buckets;
    }

    public function evaluate($other)
    {
        if (!$other instanceof ImageInterface) {
            throw \PHPUnit_Util_InvalidArgumentHelper::factory(1, 'Imagine\ImageInterface');
        }

        list($currentRed, $currentGreen, $currentBlue) = $this->normalize($this->value);
        list($otherRed, $otherGreen, $otherBlue)       = $this->normalize($other);

        $total = 0;

        foreach ($currentRed as $bucket => $count) {
            $total += abs($count - $otherRed[$bucket]);
        }

        foreach ($currentGreen as $bucket => $count) {
            $total += abs($count - $otherGreen[$bucket]);
        }

        foreach ($currentBlue as $bucket => $count) {
            $total += abs($count - $otherBlue[$bucket]);
        }

        return $total <= $this->delta;
    }

    public function toString()
    {
        return sprintf('contains color histogram identical to expected %s', \PHPUnit_Util_Type::toString($this->value));
    }

    private function normalize(ImageInterface $image)
    {
        $step    = (int) round(255 / $this->buckets);

        $red =
        $green =
        $blue = array();

        for ($i = 1; $i <= $this->buckets; $i++) {
            $range   = new Range(($i - 1) * $step, $i * $step);
            $red[]   = new Bucket($range);
            $green[] = new Bucket($range);
            $blue[]  = new Bucket($range);
        }

        foreach ($image->histogram() as $color) {
            foreach ($red as $bucket) {
                $bucket->add($color->getRed());
            }

            foreach ($green as $bucket) {
                $bucket->add($color->getGreen());
            }

            foreach ($blue as $bucket) {
                $bucket->add($color->getBlue());
            }
        }

        $total = $image->getSize()->square();

        $callback = function (Bucket $bucket) use ($total)
        {
            return count($bucket) / $total;
        };

        return array(
            array_map($callback, $red),
            array_map($callback, $green),
            array_map($callback, $blue),
        );
    }
}
