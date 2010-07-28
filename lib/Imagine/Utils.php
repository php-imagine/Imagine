<?php

namespace Imagine;

/**
 * Image utility functions
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
class Utils
{
    /**
     * Calculate integer dimensions for a box having the given aspect ratio and
     * either around (at least) or within (at most) reference dimensions.
     *
     * @param float   $boxAR       Aspect ratio (width / height)
     * @param int     $refWidth    Reference width
     * @param int     $refHeight   Reference height
     * @param boolean $around      Around if true, within if false
     * @return array (width, height)
     */
    public static function getBoxForAspectRatio($boxAR, $refWidth, $refHeight, $around)
    {
        $boxAR = (float) $boxAR;
        $refWidth = (int) $refWidth;
        $refHeight = (int) $refHeight;

        if ($boxAR <= 0) {
            throw new \InvalidArgumentException('Aspect ratio must be a positive number');
        }

        if ($refWidth <= 0 || $refHeight <= 0) {
            throw new \InvalidArgumentException('Reference dimensions must be positive integers');
        }

        $refAR = $refWidth / $refHeight;

        if ($boxAR > $refAR) {
            if ($around) {
                $boxWidth = $refHeight * $boxAR;
                $boxHeight = $refHeight;
            } else {
                $boxWidth = $refWidth;
                $boxHeight = $refWidth / $boxAR;
            }
        } elseif ($boxAR < $refAR) {
            if ($around) {
                $boxWidth = $refWidth;
                $boxHeight = $refWidth / $boxAR;
            } else {
                $boxWidth = $refHeight * $boxAR;
                $boxHeight = $refHeight;
            }
        } else {
            $boxWidth = $refWidth;
            $boxHeight = $refHeight;
        }

        return array((int) $boxWidth, (int) $boxHeight);
    }
}