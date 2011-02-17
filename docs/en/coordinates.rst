Coordinate Coordinates System
============================

The coordinate system use by Imagine is very similar to Coordinate one, with some exception:

* Coordinate system starts at x,y (0,0), which is the top level corner and extends to right and bottom accordingly
* There are no negative coordinates, a point must always be bound to the box its located at, hence 0,0 and greater
* Coordinates of the point are relative its parent bounding box

Classes
-------

The whole coordinate system is represented in a handful of classes, but most importantly - its interfaces:

* ``Imagine\Coordinate\CoordinateInterface`` - represents a single point in a bounding box
* ``Imagine\Coordinate\SizeInterface`` - represents dimensions (width, height)

CoordinateInterface
-------------------

Every coordinate contains the following methods:

* ``->getX()`` - returns horizontal position of the coordinate
* ``->getY()`` - returns vertical position of a coordinate
* ``->in(SizeInterface $box)`` - returns ``true`` if current coordinate appears to be inside of a given bounding ``$box``

Center coordinate
+++++++++++++++++

It is very well known use case when a coordinate is supposed to represent a center of something.

As part of showing off OO approach to image processing, I added a simple implementation of the core ``Imagine\Coordinate\CoordinateInterface``, which can be found at ``Imagine\Coordinate\Coordinate\Center``. The way it works is simple, it expects and instance of ``Imagine\Coordinate\SizeInterface`` in its constructor and calculates the center position based on that.

::

    <?php
    $size = new Imagine\Coordinate\Size(50, 50);
    
    $center = Imagine\Coordinate\Coordinate\Center($size);
    
    var_dump(array(
        'x' => $center->getX(),
        'y' => $center->getY(),
    ));
    
    // would output position of (x,y) 50,50

SizeInterface
-------------

Every box or image or shape has a size, size has the following methods:

* ``->getWidth()`` - returns integer width
* ``->getHeight()`` - returns integer height
* ``->scale($ratio)`` - returns a new ``SizeInterface`` instance with each side multiplied by ``$ratio``
* ``->contains(SizeInterface $box, CoordinateInterface $start = null)`` - checks that the given ``$box`` is contained inside the current ``SizeInterface`` at ``$start`` position. If no ``$start`` position is given, its assumed to be (0,0)