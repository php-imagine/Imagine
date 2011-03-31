<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine;

use Imagine\Test\ImagineTestCase;
use Imagine\Util\CompatChecker;

abstract class AbstractLibraryDependentTest extends ImagineTestCase
{
    protected function setUp() {
        parent::setUp();

        $this->checkCompatibility();
    }

    private function checkCompatibility() {
        $compat_checker = new CompatChecker($this);
        $compat_checker->check($this->getLibrary());
    }

    protected function getLibrary() {
        return $this->getLibraryByClassNamespace();
    }

    private function getLibraryByClassNamespace() {
        $parts = explode("\\", get_class($this));
        return strtolower($parts[1]);
    }
}
