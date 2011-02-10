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

use Imagine\Draw\DrawerInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\ImageInterface;

final class Drawer implements DrawerInterface
{
    private $draw;

    public function __construct(\ImagickDraw $draw)
    {
        $this->draw = $draw;
    }

    public function apply(ImageInterface $image)
    {
        if (!$image instanceof Image) {
            throw new InvalidArgumentException('Imagick\Drawer can only '.
                'process Imagick\Image instances');
        }

        $this->draw->drawImage($image->getImagick());

        return $image;
    }

    public function arc($x, $y, $width, $height, $start, $end, Color $outline)
    {
        $this->draw->arc($x, $y, $x + $width, $y + $height, $start, $end);
    }

    public function chord($x, $y, $width, $height, $start, $end, Color $outline,
    $fill = false)
    {
        // TODO Auto-generated method stub
    }

    public function ellipse($x, $y, $width, $height, Color $outline,
    $fill = false)
    {
        // TODO Auto-generated method stub
    }

    public function line($x1, $y1, $x2, $y2, Color $outline)
    {
        // TODO Auto-generated method stub
    }

    public function pieSlice($x, $y, $width, $height, $start, $end,
    Color $outline, $fill = false)
    {
        // TODO Auto-generated method stub
    }

    public function point($x, $y, Color $color)
    {
        // TODO Auto-generated method stub
    }

    public function polygon(array $coordinates, Color $outline, $fill = false)
    {
        // TODO Auto-generated method stub
    }
}
