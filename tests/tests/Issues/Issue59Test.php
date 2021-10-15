<?php

namespace Imagine\Test\Issues;

use Imagine\Driver\InfoProvider;
use Imagine\Gd\DriverInfo;
use Imagine\Gd\Imagine;
use Imagine\Test\ImagineTestCase;

/**
 * @group gd
 */
class Issue59Test extends ImagineTestCase implements InfoProvider
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\InfoProvider::getDriverInfo()
     */
    public static function getDriverInfo($required = true)
    {
        return DriverInfo::get($required);
    }

    public function testShouldResize()
    {
        $imagine = new Imagine();
        $new = $this->getTemporaryFilename('.jpeg');

        $imagine
            ->open(IMAGINE_TEST_FIXTURESFOLDER . '/sample.gif')
            ->save($new)
        ;

        $this->assertFileExists($new);
    }
}
