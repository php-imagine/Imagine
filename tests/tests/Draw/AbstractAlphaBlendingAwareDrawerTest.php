<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Draw;

use Imagine\Draw\AlphaBlendingAwareDrawerInterface;
use Imagine\Driver\InfoProvider;
use Imagine\Image\Box;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Test\ImagineTestCase;

abstract class AbstractAlphaBlendingAwareDrawerTest extends ImagineTestCase implements InfoProvider
{
    /**
     * @return \Imagine\Image\ImagineInterface
     */
    abstract protected function getImagine();

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\ImagineTestCaseBase::setUpBase()
     */
    protected function setUpBase()
    {
        parent::setUpBase();
        $drawer = $this->getImagine()->create(new Box(1, 1))->draw();
        if (!($drawer instanceof AlphaBlendingAwareDrawerInterface)) {
            $chunks = explode('\\', get_class($this->getImagine()));
            $this->markTestSkipped("The {$chunks[1]} drawer is not alphablending-aware.");
        }
    }

    public function testSettingValue()
    {
        $drawer = $this->getImagine()->create(new Box(1, 1))->draw();
        $this->assertInstanceOf('Imagine\Draw\AlphaBlendingAwareDrawerInterface', $drawer);
        /** @var \Imagine\Draw\AlphaBlendingAwareDrawerInterface $drawer */
        $originalValue = $drawer->getAlphaBlending();
        $this->assertPHPType('boolean', $originalValue, 'getAlphaBlending() should return a boolean');
        $newValue = !$originalValue;
        $this->assertSame($drawer, $drawer->setAlphaBlending($originalValue), 'setAlphaBlending() should return the same instance');
        $this->assertSame($drawer, $drawer->setAlphaBlending($newValue), 'setAlphaBlending() should return the same instance');
        $this->assertSame($newValue, $drawer->getAlphaBlending(), 'getAlphaBlending() should return the configured value');
        $newDrawer = $drawer->withAlphaBlending($originalValue);
        $this->assertNotSame($drawer, $newDrawer, 'withAlphaBlending() should return a new instance');
        $this->assertSame(get_class($drawer), get_class($newDrawer), 'withAlphaBlending() should return an instance of the same class');
        $this->assertSame($originalValue, $newDrawer->getAlphaBlending(), 'withAlphaBlending() should return a drawer with the configured value');
        $this->assertSame($newValue, $drawer->getAlphaBlending(), 'withAlphaBlending() should not change the original drawer');
    }

    public function testUsingValue()
    {
        $palette = new RGB();

        $image = $this->getImagine()->create(new Box(3, 3), $palette->color('#f00', 0));
        $image->draw()->withAlphaBlending(true)->dot(new Point(1, 1), $palette->color('#0f0', 0));
        $this->assertSame(0, $image->getColorAt(new Point(1, 1))->getAlpha(), 'Transparent on transparent should always be transparent (even with alpha blending on)');

        $image = $this->getImagine()->create(new Box(3, 3), $palette->color('#f00', 0));
        $image->draw()->withAlphaBlending(false)->dot(new Point(1, 1), $palette->color('#0f0', 0));
        $this->assertSame(0, $image->getColorAt(new Point(1, 1))->getAlpha(), 'Transparent on transparent should always be transparent (even with alpha blending off)');

        $image = $this->getImagine()->create(new Box(3, 3), $palette->color('#f00', 100));
        $image->draw()->withAlphaBlending(true)->dot(new Point(1, 1), $palette->color('#0f0', 0));
        $this->assertSame($palette->color('#f00', 100), $image->getColorAt(new Point(1, 1)), 'Drawing with a transparent color should not change the image when alpha blending is on');

        $image = $this->getImagine()->create(new Box(3, 3), $palette->color('#f00', 100));
        $image->draw()->withAlphaBlending(false)->dot(new Point(1, 1), $palette->color('#0f0', 0));
        $this->assertSame($palette->color('#0f0', 0), $image->getColorAt(new Point(1, 1)), 'Drawing with a transparent color should change the image when alpha blending is off');

        $image = $this->getImagine()->create(new Box(3, 3), $palette->color('#f00', 100));
        $image->draw()->withAlphaBlending(false)->dot(new Point(1, 1), $palette->color('#0f0', 50));
        $this->assertSame($palette->color('#0f0', 50), $image->getColorAt(new Point(1, 1)), 'Drawing with a semi-transparent color should change the image when alpha blending is off');

        $image = $this->getImagine()->create(new Box(3, 3), $palette->color('#f00', 50));
        $image->draw()->withAlphaBlending(true)->dot(new Point(1, 1), $palette->color('#0f0', 50));
        $blendedColor = $image->getColorAt(new Point(1, 1));
        $this->assertGreaterThan(10, $blendedColor->getAlpha());
        $this->assertLessThan(90, $blendedColor->getAlpha());
        $this->assertGreaterThan(0, $blendedColor->getValue(ColorInterface::COLOR_RED));
        $this->assertGreaterThan(0, $blendedColor->getValue(ColorInterface::COLOR_GREEN));
        $this->assertSame(0, $blendedColor->getValue(ColorInterface::COLOR_BLUE));
    }
}
