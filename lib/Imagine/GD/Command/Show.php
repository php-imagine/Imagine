<?php

namespace Imagine\GD\Command;

/**
 * @author Carl Helmertz <helmertz@gmail.com>
 */
class Show implements \Imagine\Command
{

    /**
     * Translate the stream to valid output, including setting a valid header
     *
     * @param \Imagine\Image $image
     * @throws \RuntimeException
     */
     public function process(\Imagine\Image $image)
     {
        if(headers_sent()) {
            throw new \RuntimeException('Can not use '.__METHOD__.' when headers already are sent');
        }
        header("Content-type: ".$image->getMimeType());

        // Calls imagejpeg() or equivalent
        $function = str_replace('/', null, $image->getMimeType());
        $function($image->getResource());

        imagedestroy($image->getResource());
    }
}
