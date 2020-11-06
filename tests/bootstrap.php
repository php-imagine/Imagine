<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// This should be already included by Composer, but it's here just in case someone calls phpunit in other ways
require_once dirname(__DIR__) . '/vendor/autoload.php';

if ((int) (ini_get('memory_limit')) < 64) {
    ini_set('memory_limit', '64M');
}

if (!class_exists('PHPUnit\Framework\Constraint\Constraint')) {
    class_alias('PHPUnit_Framework_Constraint', 'PHPUnit\Framework\Constraint\Constraint');
}

if (!class_exists('PHPUnit\Framework\Exception')) {
    class_alias('PHPUnit_Framework_Exception', 'PHPUnit\Framework\Exception');
}

if (!class_exists('PHPUnit\Framework\ExpectationFailedException')) {
    class_alias('PHPUnit_Framework_ExpectationFailedException', 'PHPUnit\Framework\ExpectationFailedException');
}

if (!class_exists('PHPUnit\Runner\Version')) {
    class_alias('PHPUnit_Runner_Version', 'PHPUnit\Runner\Version');
}

if (version_compare(PHPUnit\Runner\Version::id(), '7') >= 0) {
    class_alias('Imagine\Test\Constraint\Constraint_v2', 'Imagine\Test\Constraint\Constraint');
} else {
    class_alias('Imagine\Test\Constraint\Constraint_v1', 'Imagine\Test\Constraint\Constraint');
}

if (version_compare(PHPUnit\Runner\Version::id(), '8') >= 0) {
    class_alias('Imagine\Test\ImagineTestCase_v2', 'Imagine\Test\ImagineTestCase');
} else {
    class_alias('Imagine\Test\ImagineTestCase_v1', 'Imagine\Test\ImagineTestCase');
}

define('IMAGINE_TEST_SRCFOLDER', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

define('IMAGINE_TEST_FIXTURESFOLDER', __DIR__ . DIRECTORY_SEPARATOR . 'fixtures');

define('IMAGINE_TEST_TEMPFOLDER', __DIR__ . DIRECTORY_SEPARATOR . 'tmp');
if (!is_dir(IMAGINE_TEST_TEMPFOLDER)) {
    mkdir(IMAGINE_TEST_TEMPFOLDER);
}
