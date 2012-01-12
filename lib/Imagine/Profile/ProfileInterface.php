<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Profile;

interface ProfileInterface
{
    /**
     * Return the content of the profile
     * Inbuilt is binary string but various profiles can have specific parsers
     * to provide an improved interface
     *
     * @return string Binary string
     */
    public function get();
}
