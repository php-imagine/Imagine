<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test;

use Imagine\Image\ImagineInterface;
use Imagine\Test\Constraint\IsBoxInRange;
use Imagine\Test\Constraint\IsColorSimilar;
use Imagine\Test\Constraint\IsImageEqual;

abstract class ImagineTestCaseBase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string[]
     */
    private static $temporaryFiles = array();

    const HTTP_IMAGE = 'http://imagine.readthedocs.org/en/latest/_static/logo.jpg';

    /**
     * Asserts that two images are equal using color histogram comparison method.
     *
     * @param \Imagine\Image\ImageInterface|string $expected
     * @param \Imagine\Image\ImageInterface|string $actual
     * @param string $message
     * @param float $delta
     * @param \Imagine\Image\ImagineInterface|null $imagine
     * @param int $buckets
     */
    public static function assertImageEquals($expected, $actual, $message = '', $delta = 0.1, ImagineInterface $imagine = null, $buckets = 4)
    {
        $constraint = new IsImageEqual($expected, $delta, $imagine, $buckets);

        self::assertThat($actual, $constraint, $message);
    }

    public static function assertBoxInRange($minWidth, $maxWidth, $minHeight, $maxHeight, $actual, $message = '')
    {
        $constraint = new IsBoxInRange($minWidth, $maxWidth, $minHeight, $maxHeight);

        self::assertThat($actual, $constraint, $message);
    }

    public static function assertColorSimilar($expected, $actual, $message = '', $maxDistance = 0.0, $includeAlpha = true)
    {
        $constraint = new IsColorSimilar($expected, $maxDistance, $includeAlpha);

        self::assertThat($actual, $constraint, $message);
    }

    /**
     * @param string $suffix
     *
     * @return string
     */
    protected static function getTemporaryFilename($suffix)
    {
        $suffix = (string) $suffix;
        if ($suffix !== '' && $suffix[0] !== '.') {
            $suffix = '-' . $suffix;
        }
        $s = get_called_class();
        if (strpos($s, 'Imagine\\Test\\') === 0) {
            $s = substr($s, 13);
        }
        $filenameBase = str_replace('\\', '-', $s);
        if (PHP_VERSION_ID >= 50400) {
            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        } elseif (defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        } else {
            $bt = debug_backtrace(false);
        }
        if (isset($bt[1]['function'])) {
            $filenameBase .= '-' . $bt[1]['function'];
        }
        if (preg_match('/^(Gd|Gmagick|Imagick)-(.+)$/', $filenameBase, $m)) {
            if (preg_match('/^(.+)\.(\w+)$/', $suffix, $m2)) {
                $filenameBase = $m[2];
                $suffix = $m2[1] . '-' . $m[1] . '.' . $m2[2];
            } else {
                $filenameBase = $m[2] . '-' . $m[1];
            }
        }
        $filenameBase = IMAGINE_TEST_TEMPFOLDER . '/' . $filenameBase;
        for ($i = 0; ; $i++) {
            $filename = $filenameBase . ($i === 0 ? '' : "-{$i}") . $suffix;
            if (!in_array($filename, self::$temporaryFiles)) {
                break;
            }
        }
        if (is_file($filename)) {
            unlink($filename);
        }
        self::$temporaryFiles[] = $filename;

        return $filename;
    }

    protected static function tearDownAfterClassBase()
    {
        if (!empty(self::$temporaryFiles)) {
            $keepFiles = getenv('IMAGINE_TEST_KEEP_TEMPFILES');
            if (empty($keepFiles)) {
                foreach (self::$temporaryFiles as $temporaryFile) {
                    if (is_file($temporaryFile)) {
                        @unlink($temporaryFile);
                    }
                }
                self::$temporaryFiles = array();
            }
        }
    }

    /**
     * Override this method to implement the PHPUnit "setUp" method.
     */
    protected function setUpBase()
    {
    }

    /**
     * Override this method to implement the PHPUnit "tearDown" method.
     */
    protected function tearDownBase()
    {
    }

    /**
     * @param string $class
     * @param string|null $message
     */
    protected function isGoingToThrowException($class, $message = null)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException($class);
            if ($message !== null) {
                $this->expectExceptionMessage($message);
            }
        } else {
            parent::setExpectedException($class, $message);
        }
    }
}
