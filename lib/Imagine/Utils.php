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
     * Perform box calculations on "subject" dimensions with respect to "pov".
     *
     * @param integer $subjectX   Subject (x dimension)
     * @param integer $subjectY   Subject (y dimension)
     * @param integer $povX       POV (x dimension)
     * @param integer $povY       POV (y dimension)
     * @param boolean $atLeast    At least "subject" dimensions?
     * @param boolean $preserveAR Preserve aspect ratio?
     * @param boolean $scaleUp    Scale "subject" up?
     * @return array (x, y)
     */
    public static function calcBox($subjectX, $subjectY, $povX, $povY, $atLeast = false, $preserveAR = true, $scaleUp = true) {
        if (!($subjectX > 0 && $subjectY > 0 && $povX > 0 && $povY > 0)) {
            throw new \InvalidArgumentException('Dimensions must be positive integers');
        }

        // Initialize return values, in case resizing doesn't take place
        $calcX = $subjectX;
        $calcY = $subjectY;

        // Only resize "from" if "pov" is smaller, or explicitly scaling up
        if ($subjectX >= $povX || $subjectY >= $povX || $scaleUp) {
            // Calculate scale ratios ("pov":"from") for each dimension
            $xRatio = $povX / $subjectX;
            $yRatio = $povY / $subjectY;

            // Calculate the new size based on the chosen ratio
            if ($preserveAR) {
                $ratio = $atLeast ? max($xRatio, $yRatio) : min($xRatio, $yRatio);
                $calcX = intval($subjectX * $ratio);
                $calcY = intval($subjectY * $ratio);
            } else {
                $calcX = intval($subjectX * $xRatio);
                $calcY = intval($subjectY * $yRatio);
            }
        }

        return array($calcX, $calcY);
    }
}