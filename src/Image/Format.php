<?php

namespace Imagine\Image;

use ReflectionClass;

/**
 * Represent an image format.
 *
 * @since 1.3.0
 */
class Format
{
    const ID_AVIF = 'avif';

    const ID_BMP = 'bmp';

    const ID_GIF = 'gif';

    const ID_HEIC = 'heic';

    const ID_JPEG = 'jpeg';

    const ID_JXL = 'jxl';

    const ID_PNG = 'png';

    const ID_WBMP = 'wbmp';

    const ID_WEBP = 'webp';

    const ID_XBM = 'xbm';

    /**
     * @var \Imagine\Image\FormatList|null
     */
    private static $all = null;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var string
     */
    private $canonicalFileExtension;

    /**
     * @var string[]
     */
    private $alternativeIDs;

    /**
     * @param string $id
     * @param string $fileExtension
     * @param string $mimeType
     * @param string[] $alternativeIDs
     * @param mixed $canonicalFileExtension
     */
    private function __construct($id, $mimeType, $canonicalFileExtension, $alternativeIDs = array())
    {
        $this->id = $id;
        $this->mimeType = $mimeType;
        $this->canonicalFileExtension = $canonicalFileExtension;
        $this->alternativeIDs = $alternativeIDs;
    }

    /**
     * @return string
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getCanonicalFileExtension()
    {
        return $this->canonicalFileExtension;
    }

    /**
     * @return string[]
     */
    public function getAlternativeIDs()
    {
        return $this->alternativeIDs;
    }

    /**
     * Get a format given its ID.
     *
     * @param static|string $format the format (a Format instance of a format ID)
     *
     * @return static|null
     */
    public static function get($format)
    {
        return static::getList()->find($format);
    }

    /**
     * @return static[]
     */
    public static function getAll()
    {
        return static::getList()->getAll();
    }

    /**
     * @return \Imagine\Image\FormatList
     */
    protected static function getList()
    {
        if (self::$all !== null) {
            return self::$all;
        }
        $class = new ReflectionClass(get_called_class());
        $formats = array();
        foreach ($class->getConstants() as $constantName => $constantValue) {
            if (strpos($constantName, 'ID_') === 0) {
                $formats[] = static::create($constantValue);
            }
        }
        self::$all = new FormatList($formats);

        return self::$all;
    }

    /**
     * @param string $formatID
     *
     * @return static
     */
    protected static function create($formatID)
    {
        switch ($formatID) {
            case static::ID_JPEG:
                return new static($formatID, 'image/jpeg', 'jpg', array('jpg', 'pjpeg', 'jfif'));
            case static::ID_WBMP:
                return new static($formatID, 'image/vnd.wap.wbmp', $formatID);
            default:
                return new static($formatID, "image/{$formatID}", $formatID);
        }
    }
}
