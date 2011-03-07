Coordinate Coordinates System
============================

The coordinate system use by Imagine is very similar to Cartesian Coordinate System, with some exceptions:

* Coordinate system starts at x,y (0,0), which is the top level corner and extends to right and bottom accordingly
* There are no negative coordinates, a point must always be bound to the box its located at, hence 0,0 and greater
* Coordinates of the point are relative its parent bounding box

Classes
-------

The whole coordinate system is represented in a handful of classes, but most importantly - its interfaces:

* ``Imagine\Image\PointInterface`` - represents a single point in a bounding box

* ``Imagine\Image\BoxInterface`` - represents dimensions (width, height)

PointInterface
--------------

Every coordinate contains the following methods:

* ``->getX()`` - returns horizontal position of the coordinate

* ``->getY()`` - returns vertical position of a coordinate

* ``->in(BoxInterface $box)`` - returns ``true`` if current coordinate appears to be inside of a given bounding ``$box``

* ``->__toString()`` - returns string representation of the current ``PointInterface``, e.g. ``(0, 0)``

Center coordinate
+++++++++++++++++

It is very well known use case when a coordinate is supposed to represent a center of something.

As part of showing off OO approach to image processing, I added a simple implementation of the core ``Imagine\Image\PointInterface``, which can be found at ``Imagine\Image\Point\Center``. The way it works is simple, it expects and instance of ``Imagine\Image\BoxInterface`` in its constructor and calculates the center position based on that.

::

    <?php
    $size = new Imagine\Image\Box(50, 50);
    
    $center = new Imagine\Image\Point\Center($size);
    
    var_dump(array(
        'x' => $center->getX(),
        'y' => $center->getY(),
    ));
    
    // would output position of (x,y) 25,25

BoxInterface
-------------

Every box or image or shape has a size, size has the following methods:

* ``->getWidth()`` - returns integer width

* ``->getHeight()`` - returns integer height

* ``->scale($ratio)`` - returns a new ``BoxInterface`` instance with each side multiplied by ``$ratio``

* ``->increate($size)`` - returns a new ``BoxInterface``, with given ``$size`` added to each side

* ``->contains(BoxInterface $box, PointInterface $start = null)`` - checks that the given ``$box`` is contained inside the current ``BoxInterface`` at ``$start`` position. If no ``$start`` position is given, its assumed to be (0,0)

* ``->square()`` - returns integer square of current ``BoxInterface``, useful for determining total number of pixels in a box for example

* ``->__toString()`` - returns string representation of the current ``BoxInterface``, e.g. ``100x100 px``

A couple of words in defense
----------------------------

Having read about this, you might be wondering "Why didn't he keep width and height as simple integer parameters in every method that needed those?" or "Why is x and y coordinates are an object called Point?". These are valid questions and concerns, so let me try to explain why:

* Type-hints and validation - instead of checking for the validity of width and height (e.g. positive integers, greater than zero) or x, y (e.g. non-negative integers), I decided to move that check into constructor of ``Box`` and ``Point`` accordingly. That means, that if something passes the type-hint - a valid implementations of ``BoxInterface`` or ``PointInterface``, it is already valid.

* Utility methods - a lot of functionality, like "determine if a point is inside a given box" or "can this box fit the one we're trying to paste into it" is also to be shared in many places. The fact that these primitives are objects, let's me extract all of that duplication.

* Value objects - as you've noticed neither ``BoxInterface`` nor ``PointInterface`` along with their implementations define any setter. That means the state of those objects is immutable, so there aren't side-effects to happen and the fact that they're passed by reference, will not affect their values.

* Its OOP man, come on - nothing to add here, really.

Enjoy!