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
     * @param integer      $alpha
     */
    public function __construct($color, $alpha = 0)
    {
        $this->setColor($color);
        $this->setAlpha($alpha);
    }

    /**
     * Returns RED value of the color
     *
     * @return integer
     */
    public function getRed()
    {
        return $this->r;
    }

    /**
     * Returns GREEN value of the color
     *
     * @return integer
     */
    public function getGreen()
    {
        return $this->g;
    }

    /**
     * Returns BLUE value of the color
     *
     * @return integer
     */
    public function getBlue()
    {
        return $this->b;
    }

    /**
     * Returns percentage of transparency of the color
     *
     * @return integer
     */
    public function getAlpha()
    {
        return $this->alpha;
    }

    /**
     * Internal
     *
     * Performs checks for validity of given alpha value and sets it
     *
     * @param integer $alpha
     *
     * @throws InvalidArgumentException
     */
    private function setAlpha($alpha)
    {
        if (!is_int($alpha) || $alpha < 0 || $alpha > 100) {
            throw new InvalidArgumentException('Alpha must be an integer between 0 and 100');
        }

        $this->alpha = $alpha;
    }

    /**
     * Internal
     *
     * Performs checks for color validity (hex or array of array(R, G, B))
     *
     * @param string|array $color
     *
     * @throws InvalidArgumentException
     */
    private function setColor($color)
    {
        if (!is_string($color) && !is_array($color)) {
            throw new InvalidArgumentException(sprintf(
                'Color must be specified as a hexadecimal string or array, '.
                '%s given', gettype($color)
            ));
        }
        if (is_array($color) && count($color) !== 3) {
            throw new InvalidArgumentException(
                'Color argument if array, must look like array(R, G, B), '.
                'where R, G, B are the integer values between 0 and 255 for '.
                'red, green and blue color indexes accordingly'
            );
        }

        if (is_string($color)) {
            $color = ltrim($color, '#');

            if (strlen($color) !== 3 && strlen($color) !== 6) {
                throw new InvalidArgumentException(sprintf(
                    'Color must be a hex value in regular (6 characters) or '.
                    'short (3 charatcters) notation, "%s" given', $color
                ));
            }

            if (strlen($color) === 3) {
                $color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
            }

            $color = array_map('hexdec', str_split($color, 2));
        }

        list($this->r, $this->g, $this->b) = array_values($color);
    }

    /**
     * Returns hex representation of the color
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('#%x%x%x', $this->r, $this->g, $this->b);
    }
}
