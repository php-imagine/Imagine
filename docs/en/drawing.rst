Drawing API
===========

Imagine also provides a fully-featured drawing API, inspired by Python's PIL.
To use the api, you need to get a drawer instance from you current image instance, using ``ImageInterface::draw()`` method.

Example
-------

::

    <?php
    $image = $imagine->create(400, 300, new Color('#000'));
    
    $image->draw()
        ->ellipse(new Point(200, 150), 300, 225, new Color('fff'));
        
    $image->save('/path/to/ellipse.png');

The above example would draw an ellipse on a black 400x300px image, of white color. It would place the ellipse in the center of the image, and set its larger radius to 300px, with a smaller radius of 225px. You could also make the ellipse filled,  by passing `true` as the last parameter

Available methods
-----------------

The drawer interface defines the following methods:

* ``->arc(PointInterface $center, $width, $height, $start, $end, Color $color)`` - draws an arc, at the center in `$center` coordinates, of `$width` and `$height` radiuses, at the `$start` and `$end` angles and of `$color` color
* ``->chord(PointInterface $center, $width, $height, $start, $end, Color $color, $fill = false)`` - same as arc, except the start and end points are connected with a straight line. The `$fill` option let's you make the chord color-filled, its defaulted to `false`
* ``->ellipse(PointInterface $center, $width, $height, Color $color, $fill = false)`` draws an ellipse, parameters are same as in arc and chord, except the angles are 0 to 360 to make a full ellipse.
* ``->line(PointInterface $start, PointInterface $end, Color $outline)`` - draws a line from `$start` to `$end` of `$color` color
* ``->pieSlice(PointInterface $center, $width, $height, $start, $end, Color $color, $fill = false)`` - same as arc, but also connects the `$start` and `$end` to the ellipse center
* ``->dot(PointInterface $position, Color $color)`` - places a 1px dot at `$position`, filled with specified `$color`
* ``->polygon(array $coordinates, Color $color, $fill = false)`` - draws or outlines a polygon of a specified `$color`. The `$coordinates` array must be a collection of at least three PointInterface instances, that tell Drawer the path of the polygon its drawing
