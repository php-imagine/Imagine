<?php

namespace Imagine\Util;

use Imagine\Exception\InvalidArgumentException;

class CompatChecker {

    const REQUIRED_GD_VERSION = '2.0.1';

    /**
     * @var \PHPUnit_Framework_TestCase
     */
    private $_case;

    public function __construct(\PHPUnit_Framework_TestCase $case) {
        $this->_case = $case;
    }

    public function check($lib) {
        switch($lib) {
            case 'gd':
                $this->gd();
                break;
            case 'imagick':
            case 'gmagick':
                $this->extensionLoaded($lib);
                break;
            default:
                throw new InvalidArgumentException("Unknown library $lib, can't check compatibility.");
        }
    }

    private function gd() {
        $this->extensionLoaded('gd');
        $this->requireGdVersion();
    }

    private function extensionLoaded($lib) {
        if (!extension_loaded($lib)) {
            $this->markTestSkipped("Extension $lib is not loaded.");
        }
    }

    private function markTestSkipped($message) {
        $this->_case->markTestSkipped($message);
    }

    private function requireGdVersion() {
        if (version_compare(GD_VERSION, self::REQUIRED_GD_VERSION, '<')) {
            $message = "Gd should be %s, but you have %s.";
            $this->markTestSkipped(sprintf($message, self::REQUIRED_GD_VERSION, GD_VERSION));
        }
    }
}
