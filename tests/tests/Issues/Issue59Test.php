<?php

namespace Imagine\Test\Issues;

use Imagine\Exception\RuntimeException;
use Imagine\Gd\Imagine;
use Imagine\Test\ImagineTestCase;

/**
 * @group gd
 */
class Issue59Test extends ImagineTestCase
{
    private function getImagine()
    {
        try {
            $imagine = new Imagine();
        } catch (RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        return $imagine;
    }

    public function testShouldResize()
    {
        $imagine = $this->getImagine();
        $new = $this->getTemporaryFilename('.jpeg');

        $imagine
            ->open(IMAGINE_TEST_FIXTURESFOLDER . '/sample.gif')
            ->save($new)
        ;

        $this->assertFileExists($new);
    }
}
