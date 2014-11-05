<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

use Imagine\Exception\RuntimeException;
use Imagine\Image\AbstractFont;
use Imagine\Image\Box;

/**
 * Font implementation using the GD library
 */
final class Font extends AbstractFont
{
    /**
     * {@inheritdoc}
     */
    public function box($string, $angle = 0, $width = null)
    {
        if (!function_exists('imageftbbox')) {
            throw new RuntimeException('GD must have been compiled with `--with-freetype-dir` option to use the Font feature.');
        }

				/**
         * @see https://github.com/avalanche123/Imagine/issues/364
				 * Add a wrapText() method from Drawer using Reflection-API
         */
				if ($width !== null) {
            $string = $this->wrapText($string, $this, $angle, $width);
        }

        $angle    = -1 * $angle;
        $info     = imageftbbox($this->size, $angle, $this->file, $string);
        $xs       = array($info[0], $info[2], $info[4], $info[6]);
        $ys       = array($info[1], $info[3], $info[5], $info[7]);
        $width    = abs(max($xs) - min($xs));
        $height   = abs(max($ys) - min($ys));

        return new Box($width, $height);
    }

		/**
     * Internal
     *
     * Fits a string into box with given width
     */
    private function wrapText($string, AbstractFont $font, $angle, $width)
    {
        //Use Reflection
				$class_name = '\\Drawer'; //escaped slash
				$ref_class = new \ReflectionClass( __NAMESPACE__ . $class_name );
				if ( !$ref_class->hasMethod(__FUNCTION__) )
				{
						throw new \ReflectionException(sprintf("Method %s::%s does not exist!", $ref_class->getName() , __FUNCTION__));
				}
				$new_ref_obj = $ref_class->newInstanceWithoutConstructor();
				$ref_method_to_call = new \ReflectionMethod($new_ref_obj, __FUNCTION__);
				if( $ref_method_to_call->isPrivate() )
				{
						$ref_method_to_call->setAccessible(true);
				}

				return $ref_method_to_call->invoke($new_ref_obj, $string, $font, $angle, $width);
    }
}
