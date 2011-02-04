<?php

namespace Imagine\Filter\Basic;

class BasicFilterTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getImage()
    {
        return $this->getMock('Imagine\\ImageInterface');
    }
}
