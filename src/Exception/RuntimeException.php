<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Exception;

use Throwable;

/**
 * Imagine-specific runtime exception.
 */
class RuntimeException extends \RuntimeException implements Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $lastError = error_get_last();
        $message = isset($lastError['message'])
            ? sprintf('%s (last PHP runtime error message: %s)', $message, $lastError['message'])
            : $message
        ;

        parent::__construct($message, $code, $previous);
    }
}
