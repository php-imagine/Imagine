<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Utils;

use ErrorException;
use Exception;
use Imagine\Exception\RuntimeException;
use Throwable;

class ErrorHandling
{
    /**
     * Call a callback ignoring $flags warnings.
     *
     * @param int $flags The flags to be ignored (eg E_WARNING | E_NOTICE)
     * @param callable $callback The callable to be called
     *
     * @throws \Exception Throws an Exception if $callback throws an Exception
     * @throws \Throwable Throws an Throwable if $callback throws an Throwable
     *
     * @return mixed Returns the result of $callback
     */
    public static function ignoring($flags, $callback)
    {
        set_error_handler(
            function () {
            },
            $flags
        );
        try {
            $result = $callback();
            $exception = null;
        } catch (Exception $x) {
            $exception = $x;
        } catch (Throwable $x) {
            $exception = $x;
        }
        restore_error_handler();
        if ($exception !== null) {
            throw $exception;
        }

        return $result;
    }

    /**
     * Call a callback and throws a RuntimeException if a $flags warning is thrown.
     *
     * @param int $flags The flags to be intercepted (eg E_WARNING | E_NOTICE)
     * @param callable $callback The callable to be called
     *
     * @throws RuntimeException
     * @throws \Imagine\Exception\RuntimeException
     * @throws \Exception
     * @throws \Throwable
     *
     * @return mixed Returns the result of $callback
     */
    public static function throwingRuntimeException($flags, $callback)
    {
        set_error_handler(
            function ($errno, $errstr, $errfile, $errline) {
                if (error_reporting() !== 0) {
                    throw new RuntimeException($errstr, $errno, new ErrorException($errstr, 0, $errno, $errfile, $errline));
                }
            },
            $flags
        );
        try {
            $result = $callback();
            $exception = null;
        } catch (Exception $x) {
            $exception = $x;
        } catch (Throwable $x) {
            $exception = $x;
        }
        restore_error_handler();
        if ($exception !== null) {
            throw $exception;
        }

        return $result;
    }
}
