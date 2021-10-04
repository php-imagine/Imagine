<?php

namespace Imagine\Imagick;

class ExtensionInfo
{
    /**
     * @var string
     */
    private $imagickSemVerVersion;

    /**
     * @var string
     */
    private $imageMagickFullVersion;

    /**
     * @var string
     */
    private $imageMagickSemVerVersion;

    public function __construct(\Imagick $imagick)
    {
        $extension = new \ReflectionExtension('imagick');
        $m = null;
        $imagickVersion = $extension->getVersion();
        if (preg_match('/(\d+\.\d+\.\d+)/', $imagickVersion, $m)) {
            $this->imagickSemVerVersion = $m[0];
        } else {
            $this->imagickSemVerVersion = '';
        }
        $imageMagickVersionInfo = $imagick->getversion();
        if (preg_match('/(\d+\.\d+\.\d+)(-\d+)?/', $imageMagickVersionInfo['versionString'], $m)) {
            $this->imageMagickFullVersion = $m[1] . (isset($m[2]) ? $m[2] : '');
            $this->imageMagickSemVerVersion = $m[1];
        } else {
            $this->imageMagickFullVersion = '';
            $this->imageMagickSemVerVersion = '';
        }

        return $m[1];
    }

    /**
     * Get the major.minor.patch version of imagick.
     *
     * @example 3.4.4
     *
     * @return string
     */
    public function getImagickSemVerVersion()
    {
        return $this->imagickSemVerVersion;
    }

    /**
     * Get the full version of ImageMagick.
     *
     * @example 7.0.7-11
     *
     * @return string
     */
    public function getImageMagickFullVersion()
    {
        return $this->imageMagickFullVersion;
    }

    /**
     * Get the major.minor.patch version of ImageMagick.
     *
     * @example 7.0.7
     *
     * @return string
     */
    public function getImageMagickSemVerVersion()
    {
        return $this->imageMagickSemVerVersion;
    }
}
