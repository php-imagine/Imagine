<?php

/*
 * This file is part of the Imagine package.
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Imagine\File;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;

/**
 * Default implementation of the LoaderInterface.
 */
class Loader implements LoaderInterface
{
    /**
     * The file path.
     *
     * @var string
     */
    protected $path;

    /**
     * Does $path contain an URL?
     *
     * @var bool
     */
    protected $isUrl;

    /**
     * The loaded data.
     *
     * @var string|null
     */
    protected $data;

    /**
     * Initialize the instance.
     *
     * @param string|mixed $path the file path (or an object whose string representation is the file path)
     *
     * @throws \Imagine\Exception\InvalidArgumentException throws an InvalidArgumentException is $path is an empty string, or is not an object that has a __toString method
     */
    public function __construct($path)
    {
        if (is_object($path) && !method_exists($path, '__toString')) {
            throw new InvalidArgumentException(sprintf('$path is an object of file %s which does not implement the __toString method', get_class($path)));
        }

        $this->path = (string) $path;
        if ($this->path === '') {
            throw new InvalidArgumentException('$path is empty');
        }
        $this->isUrl = filter_var($this->path, FILTER_VALIDATE_URL);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\File\LoaderInterface::isLocalFile()
     */
    public function isLocalFile()
    {
        return !$this->isUrl;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\File\LoaderInterface::getPath()
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\File\LoaderInterface::hasReadData()
     */
    public function hasReadData()
    {
        return $this->data !== null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     * @throws \Imagine\Exception\RuntimeException
     *
     * @see \Imagine\File\LoaderInterface::getData()
     */
    public function getData()
    {
        if (!$this->hasReadData()) {
            if ($this->isLocalFile()) {
                $this->data = $this->readLocalFile();
            } else {
                $this->data = $this->readRemoteFile();
            }
        }
        return $this->data;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\File\LoaderInterface::__toString()
     */
    public function __toString()
    {
        return $this->getPath();
    }

    /**
     * Read a local file.
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return string
     */
    protected function readLocalFile()
    {
        if (!is_file($this->path)) {
            throw new InvalidArgumentException(sprintf('The file "%s" does not exist.', $this->path));
        }
        if (!is_readable($this->path)) {
            throw new InvalidArgumentException(sprintf('The file "%s" is not readable.', $this->path));
        }
        $data = @file_get_contents($this->path);
        if ($data === false) {
            throw new InvalidArgumentException(sprintf('Failed to read from file "%s".', $this->path));
        }
        return $data;
    }

    /**
     * Read a remote file.
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return string
     */
    protected function readRemoteFile()
    {
        if (function_exists('curl_init')) {
            return $this->readRemoteFileWithCurl();
        }
        return $this->readRemoteFileWithFileGetContents();
    }

    /**
     * Read a remote file using the cURL extension.
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return string
     */
    protected function readRemoteFileWithCurl()
    {
        $curl = @curl_init($this->path);
        if ($curl === false) {
            throw new RuntimeException('curl_init() failed.');
        }
        if (!@curl_setopt($curl, CURLOPT_RETURNTRANSFER, true)) {
            throw new RuntimeException('curl_setopt(CURLOPT_RETURNTRANSFER) failed.');
        }
        if (!@curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Encoding: identity'))) {
            throw new RuntimeException('curl_setopt(CURLOPT_HTTPHEADER) failed.');
        }
        if (!@curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true)) {
            throw new RuntimeException('curl_setopt(CURLOPT_FOLLOWLOCATION) failed.');
        }

        $response = @curl_exec($curl);
        if ($response === false) {
            $errorMessage = curl_error($curl);
            if ($errorMessage === '') {
                $errorMessage = 'curl_exec() failed.';
            }
            $errorCode = curl_errno($curl);
            curl_close($curl);
            throw new RuntimeException($errorMessage, $errorCode);
        }
        $responseInfo = curl_getinfo($curl);
        curl_close($curl);
        if ($responseInfo['http_code'] == 404) {
            throw new InvalidArgumentException(sprintf('The file "%s" does not exist.', $this->path));
        }
        if ($responseInfo['http_code'] < 200 || $responseInfo['http_code'] >= 300) {
            throw new InvalidArgumentException(sprintf('Failed to download "%s": %s', $this->path, $responseInfo['http_code']));
        }

        return $response;
    }

    /**
     * Read a remote file using the file_get_contents.
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return string
     */
    protected function readRemoteFileWithFileGetContents()
    {
        $data = @file_get_contents($this->path);
        if ($data === false) {
            if (isset($http_response_header) && is_array($http_response_header) && isset($http_response_header[0]) && preg_match('/^HTTP\/\d+(?:\.\d+)*\s+(\d+\s+\w.*)/i', $http_response_header[0], $matches)) {
                throw new InvalidArgumentException(sprintf('Failed to read from URL %s: %s', $this->path, $matches[1]));
            }
            throw new InvalidArgumentException(sprintf('Failed to read from URL %s', $this->path));
        }
        return $data;
    }
}
