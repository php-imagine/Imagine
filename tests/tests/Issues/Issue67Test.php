<?php

namespace Imagine\Test\Issues;

use Imagine\Driver\InfoProvider;
use Imagine\Gd\DriverInfo;
use Imagine\Gd\Imagine;
use Imagine\Test\ImagineTestCase;

/**
 * @group gd
 */
class Issue67Test extends ImagineTestCase implements InfoProvider
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

    public function testShouldThrowExceptionNotError()
    {
        $this->isGoingToThrowException('Imagine\Exception\RuntimeException');
        $invalidPath = '/thispathdoesnotexist';

        $imagine = new Imagine();

        $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/large.jpg')
            ->save($invalidPath . '/myfile.jpg');
    }
}
