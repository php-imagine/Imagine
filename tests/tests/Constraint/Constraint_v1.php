<?php

namespace Imagine\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint as PHPUnitConstraint;

abstract class Constraint_v1 extends PHPUnitConstraint
{
    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\Constraint\Constraint::matches()
     */
    protected function matches($other)
    {
        return $this->_matches($other);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\Constraint\Constraint::failureDescription()
     */
    public function failureDescription($other)
    {
        return $this->_failureDescription($other);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\Constraint\Constraint::toString()
     */
    public function toString()
    {
        return $this->_toString();
    }
}
