<?php

namespace Imagine\Test\Issues;

use Imagine\Exception\NotSupportedException;
use Imagine\Exception\RuntimeException;
use Imagine\Gmagick\DriverInfo as GmagickDriverInfo;
use Imagine\Gmagick\Imagine as GmagickImagine;
use Imagine\Imagick\DriverInfo as ImagickDriverInfo;
use Imagine\Imagick\Imagine as ImagickImagine;
use Imagine\Test\ImagineTestCase;

class Issue131Test extends ImagineTestCase
{
    private function getTemporaryDir()
    {
        $tempDir = tempnam(sys_get_temp_dir(), 'imagine');

        unlink($tempDir);
        mkdir($tempDir);

        return $tempDir;
    }

    private function getDirContent($dir)
    {
        $filenames = array();

        foreach (new \DirectoryIterator($dir) as $fileinfo) {
            if ($fileinfo->isFile()) {
                $filenames[] = $fileinfo->getPathname();
            }
        }

        return $filenames;
    }

    private function getImagickImagine($file)
    {
        try {
            ImagickDriverInfo::get()->checkVersionIsSupported();
        } catch (NotSupportedException $x) {
            $this->markTestSkipped($x->getMessage());
        }
        $imagine = new ImagickImagine();
        try {
            return $imagine->open($file);
        } catch (RuntimeException $x) {
            $this->markTestSkipped($x->getMessage());
        }
    }

    private function getGmagickImagine($file)
    {
        try {
            GmagickDriverInfo::get()->checkVersionIsSupported();
        } catch (NotSupportedException $x) {
            $this->markTestSkipped($x->getMessage());
        }
        $imagine = new GmagickImagine();
        try {
            return $imagine->open($file);
        } catch (RuntimeException $x) {
            $this->markTestSkipped($x->getMessage());
        }
    }

    /**
     * @doesNotPerformAssertions
     * @group imagick
     */
    public function testShouldSaveOneFileWithImagick()
    {
        $dir = realpath($this->getTemporaryDir());
        $targetFile = $dir . '/myfile.png';

        $imagine = $this->getImagickImagine(__DIR__ . '/multi-layer.psd');

        $imagine->save($targetFile);

        if (!$this->probeOneFileAndCleanup($dir, $targetFile)) {
            $this->fail('Imagick failed to generate one file');
        }
    }

    /**
     * @group gmagick
     */
    public function testShouldSaveOneFileWithGmagick()
    {
        $dir = realpath($this->getTemporaryDir());
        $targetFile = $dir . '/myfile.png';

        $imagine = $this->getGmagickImagine(__DIR__ . '/multi-layer.psd');

        $imagine->save($targetFile);

        $this->assertTrue($this->probeOneFileAndCleanup($dir, $targetFile), 'Gmagick failed to generate one file');
    }

    private function probeOneFileAndCleanup($dir, $targetFile)
    {
        $retval = true;
        $files = $this->getDirContent($dir);
        $retval = $retval && count($files) === 1;
        $file = current($files);
        $retval = $retval && str_replace('/', DIRECTORY_SEPARATOR, $targetFile) === str_replace('/', DIRECTORY_SEPARATOR, $file);

        foreach ($files as $file) {
            unlink($file);
        }

        rmdir($dir);

        return $retval;
    }
}
