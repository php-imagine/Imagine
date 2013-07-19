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

use OutOfBoundsException as BaseOutOfBoundsException;

/**
 * Imagine-specific out of bounds exception
 */
class OutOfBoundsException extends BaseOutOfBoundsException implements Exception
{
}
