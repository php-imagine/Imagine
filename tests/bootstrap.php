<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ((int) (ini_get('memory_limit')) < 64) {
    ini_set('memory_limit', '64M');
}

$loader = require __DIR__ . '/../vendor/autoload.php';
/* @var Composer\Autoload\ClassLoader $loader */
$loader->addPsr4('Imagine\\Test\\', __DIR__ . DIRECTORY_SEPARATOR . 'tests');

if (!class_exists('PHPUnit\Framework\Constraint\Constraint')) {
    class_alias('PHPUnit_Framework_Constraint', 'PHPUnit\Framework\Constraint\Constraint');
}

if (!class_exists('PHPUnit\Util\InvalidArgumentHelper')) {
    class_alias('PHPUnit_Util_InvalidArgumentHelper', 'PHPUnit\Util\InvalidArgumentHelper');
}

if (!class_exists('PHPUnit\Framework\Exception')) {
    class_alias('PHPUnit_Framework_Exception', 'PHPUnit\Framework\Exception');
}

if (!class_exists('PHPUnit\Framework\ExpectationFailedException')) {
    class_alias('PHPUnit_Framework_ExpectationFailedException', 'PHPUnit\Framework\ExpectationFailedException');
}

define('IMAGINE_TEST_SRCFOLDER', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

define('IMAGINE_TEST_FIXTURESFOLDER', __DIR__ . DIRECTORY_SEPARATOR . 'fixtures');

define('IMAGINE_TEST_TEMPFOLDER', __DIR__ . DIRECTORY_SEPARATOR . 'tmp');
if (!is_dir(IMAGINE_TEST_TEMPFOLDER)) {
    mkdir(IMAGINE_TEST_TEMPFOLDER);
}
