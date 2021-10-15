<?php

namespace Imagine\Test\Issues;

use Imagine\Driver\InfoProvider;
use Imagine\Gd\DriverInfo;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Test\ImagineTestCase;

/**
 * @group gd
 */
class Issue17Test extends ImagineTestCase implements InfoProvider
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
        $size = new Box(100, 10);
        $imagine = new Imagine();

        $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/large.jpg')
            ->thumbnail($size, ImageInterface::THUMBNAIL_OUTBOUND)
            ->save(IMAGINE_TEST_FIXTURESFOLDER . '/resized.jpg');

        $this->assertFileExists(IMAGINE_TEST_FIXTURESFOLDER . '/resized.jpg');
        $this->assertEquals(
            $size,
            $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/resized.jpg')->getSize()
        );

        unlink(IMAGINE_TEST_FIXTURESFOLDER . '/resized.jpg');
    }
}
