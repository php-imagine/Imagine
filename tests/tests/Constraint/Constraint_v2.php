<?php

namespace Imagine\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint as PHPUnitConstraint;

abstract class Constraint_v2 extends PHPUnitConstraint
{
    public function __construct()
    {
        if (method_exists('PHPUnit\Framework\Constraint\Constraint', '__construct')) {
            parent::__construct();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\Constraint\Constraint::matches()
     */
    protected function matches($other): bool
    {
        return $this->_matches($other);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\Constraint\Constraint::failureDescription()
     */
    public function failureDescription($other): string
    {
        return $this->_failureDescription($other);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\Constraint\Constraint::toString()
     */
    public function toString(): string
    {
        return $this->_toString();
    }
}
