<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Imagick;

use Imagine\Test\ImagineTestCase;
use Imagine\Profile\Profile;

class ImageProfilesTest extends ImagineTestCase 
{
    protected $testOn = 'tests/Imagine/Fixtures/profiles.jpg';
    protected $saveTo = 'tests/Imagine/Fixtures/profiles-copy.jpg';

    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
        }
    }

    protected function getImagine()
    {
        return new Imagine();
    }


    public function testImageCopyMaintainsProfiles() {
        $imagine = $this->getImagine();
        $image = $imagine->open($this->testOn);
        $expected = $image->getProfile();

        $copy = $image->copy();

        // Compare to self as an assertion that our compare methods work
        $this->assertProfilesEquals($image->getProfile(), $image->getProfile(), 'Sanity check, self == self');

        $this->assertProfilesEquals($expected, $copy->getProfile(), 'Object copy');
    }

    protected function assertProfilesEquals($expect, $actual, $mode = '') {
        foreach ($expect as $key => $profile) {
            $this->assertTrue(isset($actual[$key]), "Profile $key missing: $mode");
            $this->assertEquals($profile->get(), $actual[$key]->get(), "Profile $key doesnt match expected: $mode. Content is base64 encoded");
        }
    }
}
