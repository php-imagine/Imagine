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

class ProfileTest extends ImagineTestCase
{
    protected $saveTo = 'tests/Imagine/Fixtures/profiles-copy.jpg';
    protected $testOn = 'tests/Imagine/Fixtures/profiles.jpg';
    protected $mockProfile = 'tests/Imagine/Fixtures/mock-profile';

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

    public function testICCProfileHasChecksReturnsCorrectly()
    {
        $this->assertTrue(Profile::has('icc', 'srgb'));
        $this->assertFalse(Profile::has('icc', 'nope'));
    }

    public function testLoadAndGetProfile()
    {
        $expect = 'lib/Imagine/Profile/profiles/sRGB_IEC61966-2-1_no_black_scaling.icc';
        $profile = Profile::open('icc', 'srgb');
        $this->assertTrue($profile instanceof Profile);
        $this->assertEquals(file_get_contents($expect), $profile->get());
    }

    public function testRegisterNewProfile()
    {
        $this->mockProfile = realpath($this->mockProfile);
        Profile::register('icc', 'test', $this->mockProfile);
        $profile = Profile::open('icc', 'test');
        $this->assertEquals(file_get_contents($this->mockProfile), $profile->get());
    }

    public function testReadImageProfiles()
    {
        $imagine = $this->getImagine();
        $expected = array('8bim', 'exif', 'icc', 'iptc', 'xmp');
        $image = $imagine->open($this->testOn);
        $this->assertEquals($expected, array_keys($image->getProfile()));
        $this->assertEquals(array('icc'), array_keys($image->getProfile('icc')));
    }
}
