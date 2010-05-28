<?php

namespace Imagine;

class UploadedImage extends StandardImage
{

    public function __construct(array $data)
    {
        if (isset ($data['error']) && $data['error'] !== \UPLOAD_ERR_OK) {
            switch ($data['error']) {
                case \UPLOAD_ERR_INI_SIZE:
                case \UPLOAD_ERR_FORM_SIZE:
                    $message = 'The image exceeds maximum upload size';
                case \UPLOAD_ERR_PARTIAL:
                    $message = 'The image was only partially uploaded';
                case \UPLOAD_ERR_NO_FILE:
                    $message = 'The image wasn\'t uploaded';
                case \UPLOAD_ERR_NO_TMP_DIR:
                    $message = 'No temp. directory found';
                case \UPLOAD_ERR_CANT_WRITE:
                    $message = 'Could not save file to disk';
                case \UPLOAD_ERR_EXTENSION:
                    $message = 'PHP extension stopper the upload';
                default:
                    $message = 'Unknown error';
                break;
            }
            throw new \RuntimeException($message);
        }
        parent::__construct($data['tmp_name']);
		$pathinfo = pathinfo($data['name']);
        $this->setName(basename($data['name'], '.' . $pathinfo['extension']));
    }
}
