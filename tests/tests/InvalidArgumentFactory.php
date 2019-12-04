<?php

namespace Imagine\Test;

class InvalidArgumentFactory
{
    /**
     * @param int $argument
     * @param string $type
     *
     * @return \PHPUnit\Framework\InvalidArgumentException
     */
    public static function create($argument, $type)
    {
        if (class_exists('PHPUnit\Framework\InvalidArgumentException')) {
            return \PHPUnit\Framework\InvalidArgumentException::create($argument, $type);
        }
        if (class_exists('PHPUnit\Util\InvalidArgumentHelper')) {
            return \PHPUnit\Util\InvalidArgumentHelper::factory($argument, $type);
        }

        return \PHPUnit_Util_InvalidArgumentHelper::factory($argument, $type);
    }
}
