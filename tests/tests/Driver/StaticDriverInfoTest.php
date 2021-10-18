<?php

namespace Imagine\Test\Driver;

use Imagine\Test\ImagineTestCase;
use ReflectionClass;

class StaticDriverInfoTest extends ImagineTestCase
{
    public function testNotDuplicatedFeatureIDs()
    {
        $class = new ReflectionClass('Imagine\Driver\Info');
        $featuresByID = array();
        $duplicatedFeatures = array();
        foreach ($class->getConstants() as $constantName => $constantValue) {
            if (strpos($constantName, 'FEATURE_') !== 0) {
                continue;
            }
            if (isset($featuresByID[$constantValue])) {
                $duplicatedFeatures[] = "The features {$featuresByID[$constantValue]} and {$constantName} has the same value ({$constantValue})";
            } else {
                $featuresByID[$constantValue] = $constantName;
            }
        }
        $this->assertSame(array(), $duplicatedFeatures);
    }
}
