<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine;

use Imagine\Exception\InvalidArgumentException;

final class Color
{
    private $r, $g, $b;
    private $alpha;

    /**
     * Constructs image color, e.g.:
     *     - new Color('fff') - will produce non-transparent white color
     *     - new Color('ffffff', 50) - will product 50% transparent white
     *     - new Color(array(255, 255, 255)) - another way of getting white
     *
     * @param array|string $color
     * @param int          $alpha
     */
    public function __construct($color, $alpha = 100)
    {
        $this->setColor($color);
        $this->setAlpha($alpha);
    }

    public function getRed()
    {
        return $this->r;
    }

    public function getGreen()
    {
        return $this->g;
    }

    public function getBlue()
    {
        return $this->b;
    }

    public function getAlpha()
    {
        return $this->alpha;
    }

    private function setAlpha($alpha)
    {
        if (!is_int($alpha) || $alpha < 0 || $alpha > 100) {
            throw new InvalidArgumentException('Alpha must be an integer between 0 and 100');
        }

        $this->alpha = $alpha;
    }

    private function setColor($color)
    {
        if (!is_string($color) && !is_array($color)) {
            throw new InvalidArgumentException(sprintf('Color must be '.
                'specified as a hexadecimal string or array, %s given',
                gettype($color)));
        }
        if (is_array($color) && count($color) !== 3) {
            throw new InvalidArgumentException('Color argument if array, must '.
                'look like array(R, G, B), where R, G, B are the integer '.
                'values between 0 and 255 for red, green and blue color '.
                'indexes accordingly');
        }

        if (is_string($color)) {
            if ($color[0] === '#') {
                $color = substr($color, 1);
            }

            if (strlen($color) !== 3 && strlen($color) !== 6) {
                throw new InvalidArgumentException(sprintf('Color must be a '.
                    'hex value in regular (6 characters) or short (3 '.
                    'charatcters) notation, "%s" given', $color));
            }

            if (strlen($color) === 3) {
                $color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
            }

            $color = array_map('hexdec', str_split($color, 2));
        }

        list($this->r, $this->g, $this->b) = array_values($color);
    }
}
