<?php

namespace Imagine\GD\Command;

use Imagine\GD\Utils;

/**
 * GD save command
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
class Save implements \Imagine\Command
{
    /**
     * Image save path
     *
     * @var string
     */
    protected $path;

    /**
     * Image type
     *
     * @var int
     */
    protected $type;

    /**
     * Save function parameters
     *
     * @var array
     */
    protected $saveParams;

    /**
     * Constructs a save operation.
     *
     * If $saveParams is given, it will be used for additional parameters to the
     * GD image save function, after the resource and filename parameters.
     *
     * @param string $path       Image save path
     * @param int    $type       Image type (optional)
     * @param array  $saveParams Save function parameters (optional)
     */
    public function __construct($path = null, $type = null, array $saveParams = array())
    {
        $this->path = $path;
        $this->type = $type;
        $this->saveParams = $saveParams;
    }

    /**
     * Process the save operation.
     *
     * If the directory for the save path does not exist, it will be created.
     *
     * @param \Imagine\Image $image
     * @throws \RuntimeException
     */
    public function process(\Imagine\Image $image)
    {
        try {
            $saveFunction = Utils::getSaveFunction($this->type ?: $image->getType());
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $path = $this->path ?: $image->getPath();
        $saveParams = array_merge(array($image->getResource(), $path), $this->saveParams);

        if(array_key_exists('quality', $saveParams)) {
            $saveParams['quality'] = Utils::getSaveQuality($this->type ?: $image->getType(), $saveParams['quality']);
        }

        if (! is_dir($pathDir = dirname($path))) {
            if (! @mkdir($pathDir, 0777, true)) {
                throw new \RuntimeException('Cannot create directory: ' . $pathDir);
            }
        }

        if (! call_user_func_array($saveFunction, $saveParams)) {
            throw new \RuntimeException('Could not save the image: ' . $path);
        }
    }
}
