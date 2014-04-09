<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

/**
 * Default implementation fo box factory
 */
final class BoxFactory implements BoxFactoryInterface
{
    private static $instance;
    
    /**
     * @return BoxFactory
     */
    public static function instance()
    {
        if(self::$instance === null)
        {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createBox($width, $height)
    {
        return new Box($width, $height);
    }
}