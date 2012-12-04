<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

use Imagine\Image\Fill\Gradient\Horizontal;
use Imagine\Image\Fill\Gradient\Vertical;
use Imagine\Image\Point\Center;
use Imagine\Test\ImagineTestCase;

abstract class AbstractImageTest extends ImagineTestCase
{
    public function testRotateWithNoBackgroundColor()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');
        $image->rotate(90);

        $size = $image->getSize();

        $this->assertSame(126, $size->getWidth());
        $this->assertSame(364, $size->getHeight());
    }

    public function testCopyResizedImageToImage()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');
        $size  = $image->getSize();

        $image->paste(
                $image->copy()
                    ->resize($size->scale(0.5))
                    ->flipVertically(),
                new Center($size)
            );
    }

    public function testThumbnailGeneration()
    {
        $factory = $this->getImagine();
        $image   = $factory->open('tests/Imagine/Fixtures/google.png');
        $inset   = $image->thumbnail(new Box(50, 50), ImageInterface::THUMBNAIL_INSET);

        $size = $inset->getSize();

        unset($inset);

        $this->assertEquals(50, $size->getWidth());
        $this->assertEquals(17, $size->getHeight());

        $outbound = $image->thumbnail(new Box(50, 50), ImageInterface::THUMBNAIL_OUTBOUND);

        $size = $outbound->getSize();

        unset($outbound);
        unset($image);

        $this->assertEquals(50, $size->getWidth());
        $this->assertEquals(50, $size->getHeight());
    }

    public function testCropResizeFlip()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png')
            ->crop(new Point(0, 0), new Box(126, 126))
            ->resize(new Box(200, 200))
            ->flipHorizontally();

        $size = $image->getSize();

        unset($image);

        $this->assertEquals(200, $size->getWidth());
        $this->assertEquals(200, $size->getHeight());
    }

    public function testCreateAndSaveEmptyImage()
    {
        $factory = $this->getImagine();
        $image   = $factory->create(new Box(400, 300), new Color('000'));

        $size  = $image->getSize();

        unset($image);

        $this->assertEquals(400, $size->getWidth());
        $this->assertEquals(300, $size->getHeight());
    }

    public function testCreateTransparentGradient()
    {
        $factory = $this->getImagine();
        $size    = new Box(100, 50);
        $image   = $factory->create($size, new Color('f00'));

        $image->paste(
                $factory->create($size, new Color('ff0'))
                    ->applyMask(
                        $factory->create($size)
                            ->fill(
                                new Horizontal(
                                    $image->getSize()->getWidth(),
                                    new Color('fff'),
                                    new Color('000')
                                )
                            )
                    ),
                new Point(0, 0)
            );

        $size = $image->getSize();

        unset($image);

        $this->assertEquals(100, $size->getWidth());
        $this->assertEquals(50, $size->getHeight());
    }

    public function testMask()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $image->applyMask($image->mask())
            ->save('tests/Imagine/Fixtures/mask.png');

        $size = $factory->open('tests/Imagine/Fixtures/mask.png')
            ->getSize();

        $this->assertEquals(364, $size->getWidth());
        $this->assertEquals(126, $size->getHeight());

        unlink('tests/Imagine/Fixtures/mask.png');
    }

    public function testColorHistogram()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $this->assertEquals(6438, count($image->histogram()));
    }

    public function testImageResolutionChange()
    {
        $imagine = $this->getImagine();
        $image = $imagine->open('tests/Imagine/Fixtures/resize/210-design-19933.jpg');
        $outfile = 'tests/Imagine/Fixtures/resize/reduced.jpg';
        $image->save($outfile, array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 144,
            'resolution-y' => 144
        ));

        if ($imagine instanceof \Imagine\Imagick\Imagine) {
            $i = new \Imagick($outfile);
            $info = $i->identifyimage();
            $this->assertEquals(144, $info['resolution']['x']);
            $this->assertEquals(144, $info['resolution']['y']);
        }
        if ($imagine instanceof \Imagine\Gmagick\Imagine) {
            $i = new \Gmagick($outfile);
            $info = $i->getimageresolution();
            $this->assertEquals(144, $info['x']);
            $this->assertEquals(144, $info['y']);
        }

        unlink($outfile);
    }

    public function testInOutResult(){

        $this->processInOut("trans", "png","png");
        $this->processInOut("trans", "png","gif");
        $this->processInOut("trans", "png","jpg");
        $this->processInOut("anima", "gif","png");
        $this->processInOut("anima", "gif","gif");
        $this->processInOut("anima", "gif","jpg");
        $this->processInOut("trans", "gif","png");
        $this->processInOut("trans", "gif","gif");
        $this->processInOut("trans", "gif","jpg");
        $this->processInOut("large", "jpg","png");
        $this->processInOut("large", "jpg","gif");
        $this->processInOut("large", "jpg","jpg");
    }

    public function testLayerReturnsALayerInterface()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $this->assertInstanceOf('Imagine\\Image\\LayersInterface', $image->layers());
    }

    public function testCountAMonoLayeredImage()
    {
        $this->assertEquals(1, count($this->getMonoLayeredImage()->layers()));
    }

    public function testCountAMultiLayeredImage()
    {
        if (!$this->supportMultipleLayers()) {
            $this->markTestSkipped('This driver does not support multiple layers');
        }

        $this->assertGreaterThan(1, count($this->getMultiLayeredImage()->layers()));
    }

    public function testLayerOnMonoLayeredImage()
    {
        foreach ($this->getMonoLayeredImage()->layers() as $layer) {
            $this->assertInstanceOf('Imagine\\Image\\ImageInterface', $layer);
            $this->assertCount(1, $layer->layers());
        }
    }

    public function testLayerOnMultiLayeredImage()
    {
        foreach ($this->getMultiLayeredImage()->layers()  as $layer) {
            $this->assertInstanceOf('Imagine\\Image\\ImageInterface', $layer);
            $this->assertCount(1, $layer->layers());
        }
    }

    private function getMonoLayeredImage()
    {
        return $this->getImagine()->open('tests/Imagine/Fixtures/google.png');
    }

    private function getMultiLayeredImage()
    {
        return $this->getImagine()->open('tests/Imagine/Fixtures/cat.gif');
    }

    protected function processInOut($file, $in, $out)
    {
        $factory = $this->getImagine();
        $class = preg_replace('/\\\\/', "_", get_called_class());
        $image = $factory->open('tests/Imagine/Fixtures/'.$file.'.'.$in);
        $thumb = $image->thumbnail(new Box(50, 50), ImageInterface::THUMBNAIL_OUTBOUND);
        $thumb->save("tests/Imagine/Fixtures/results/in_out/{$class}_{$file}_from_{$in}_to.{$out}");

    }

    abstract protected function getImagine();
    abstract protected function supportMultipleLayers();
}
