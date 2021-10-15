<?php

namespace Imagine\Image;

/**
 * Holds a list of image formats.
 *
 * @since 1.3.0
 */
class FormatList
{
    /**
     * @var \Imagine\Image\Format[]
     */
    private $formats;

    public function __construct(array $formats)
    {
        $this->formats = $formats;
    }

    /**
     * @return \Imagine\Image\Format[]
     */
    public function getAll()
    {
        return $this->formats;
    }

    /**
     * @return string[]
     */
    public function getAllIDs()
    {
        $result = array();
        foreach ($this->getAll() as $format) {
            $result[] = $format->getID();
        }

        return $result;
    }

    /**
     * Get a format given its ID.
     *
     * @param \Imagine\Image\Format|string $format the format (a Format instance of a format ID)
     *
     * @return \Imagine\Image\Format|null
     */
    public function find($format)
    {
        if (is_string($format)) {
            $format = strtolower(trim($format));
            if ($format === '') {
                return null;
            }
            foreach ($this->getAll() as $f) {
                if ($f->getID() === $format || in_array($format, $f->getAlternativeIDs(), true)) {
                    return $f;
                }
            }

            return null;
        }

        return in_array($format, $this->getAll(), true) ? $format : null;
    }
}
