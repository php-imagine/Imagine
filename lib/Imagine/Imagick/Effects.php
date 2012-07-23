<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Imagick;

use Imagine\Effects\EffectsInterface;

class Effects implements EffectsInterface
{
    private $imagick;

    public function __construct(\Imagick $imagick)
    {
        $this->imagick = $imagick;
    }

    /**
     * {@inheritdoc}
     */
    public function gamma($correction)
    {
        try {
            $this->imagick->gammaImage($correction, \Imagick::CHANNEL_ALL);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to negate image');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function negative()
    {
        try {
            $this->imagick->negateImage(false, \Imagick::CHANNEL_ALL);
        } catch (\ImagickException $e) {
            throw new RuntimeException('Failed to negate image');
        }

        return $this;
    }
}
