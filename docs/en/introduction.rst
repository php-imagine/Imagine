Introduction
============

Basic usage
-----------

Open Existing Images
++++++++++++++++++++

To open an existing image, all you need is to instantiate an image factory and invoke ``Imagine::open()`` with ``$path`` to image as the  argument

::

    <?php
    $imagine = new Imagine\Gd\Imagine();
    // or
    $imagine = new Imagine\Imagick\Imagine();
    
    $image = $imagine->open('/path/to/image.jpg');

The ``Imagine::open()`` method may throw one of the following exceptions:

* ``Imagine\Exception\InvalidArgumentException``
* ``Imagine\Exception\RuntimeException``

Now that you've opened an image, you can perform manipulations on it:

::

    <?php
    $image->resize(15, 25)
        ->rotate(45)
        ->crop(0, 0, 45, 45)
        ->save('/path/to/new/image.jpg');

.. TIP::
    Read more about Image_

Create New Images
+++++++++++++++++

Imagine also lets you create new, empty images. The following example creates an empty image of width 400px and height 300px:

::

    <?php
    $image = $imagine->create(400, 300);

You can optionally specify the fill color for the new image, which defaults to opaque white. The following example creates a new image with a fully-transparent black background:

::

    <?php
    $image = $imagine->create(400, 300, new Imagine\Color('000', 100));

Color Class
+++++++++++

Color is a class in Imagine, which takes two arguments in its constructor: the RGB color code and a transparency percentage. The following examples are equivalent ways of defining a fully-transparent white color.

::

    <?php
    $white = new Imagine\Color('fff', 100);
    $white = new Imagine\Color('ffffff', 100);
    $white = new Imagine\Color('#fff', 100);
    $white = new Imagine\Color('#ffffff', 100);
    $white = new Imagine\Color(array(255, 255, 255), 100);

After you have instantiated a color, you can easily get its Red, Green, Blue and Alpha (transparency) values:

::

    <?php
    var_dump(array(
        'R' => $white->getRed(),
        'G' => $white->getGreen(),
        'B' => $white->getBlue(),
        'A' => $white->getAlpha()
    ));

Point Class
+++++++++++

Every coordinate location (x, y) in Imagine is represented by a ``Point`` instance.

``Point`` is a simple and light-weight value object, that takes values for x and y coordinate it represents as its constructor arguments. After the ``Point`` is constructed, ``Point::getX()`` and ``Point::getY()`` can be used to get the appropriate values back.

::

    <?php
    $point = new Point(0, 0);
    
    var_dump(array(
        'x' => $point->getX(),
        'y' => $point->getY(),
    ));

Advanced Example - An Image Collage
-----------------------------------

Assume we were given the not-so-easy task of creating a four-by-four collage of 16 student portraits for a school yearbook.  Each photo is 30x40 px and we need four rows and columns in our collage, so the final product will be 120x160 px.

Here is how we would approach this problem with Imagine.

::

    <?php
    use Imagine\Point;
    
    // make an empty image (canvas) 120x160px
    $collage = $imagine->create(120, 160);
    
    // starting coordinates (in pixels) for inserting the first image
    $x = 0;
    $y = 0;
    
    foreach (glob('/path/to/people/photos/*.jpg') as $path) {
        // open photo
        $photo = $imagine->open($path);
        
        // paste photo at current position
        $collage->paste($photo, new Point($x, $y));
        
        // move position by 30px to the right
        $x += 30;
        
        if ($x >= 120) {
            // we reached the right border of our collage, so advance to the
            // next row and reset our column to the left.
            $y += 40;
            $x = 0;
        }
        
        if ($y >= 160) {
            break; // done
        }
    }
    
    $collage->save('/path/to/collage.jpg');

Image Transformations, aka Lazy Processing
------------------------------------------

Sometimes we're not confortable with opening an image inline, and would like to apply some pre-defined operations in the lazy manner. 

For that, Imagine provides so-called image transformations.

Image transformation is implemented via the ``Filter\Transformation`` class, which mostly conforms to ``ImageInterface`` and can be used interchangeably with it. The main difference is that transformations may be stacked and performed on a real ``ImageInterface`` instance later using the ``Transformation::apply()`` method.

Example of a naive thumbnail implementation:

::

    <?php
    $transformation = new Imagine\Filter\Transformation();
    
    $transformation->thumbnail(30, 30)
        ->save('/path/to/resized/thumbnail.jpg');
    
    $transformation->apply($imagine->open('/path/to/image.jpg'));

The result of ``apply()`` is the modified image instance itself, so if we wanted to create a mass-processing thumbnail script, we would do something like the following:

::

    <?php
    $transformation = new Imagine\Filter\Transformation();
    
    $transformation->thumbnail(30, 30);
    
    foreach (glob(/path/to/lots/of/images/*.jpg) as $path) {
        $transformation->apply($imagine->open($path))
            ->save('/path/to/resized/'.md5($path).'.jpg');
    }

The ``Filter\Tranformation`` class itself is simply a very specific implementation of ``FilterInterface``, which is a more generic interface, that let's you pre-define certain operations and variable calculations and apply them to an ``ImageInterface`` instance later.

Filters
-------

As we already know, ``Filter\Transformation`` is just a very special case of ``Filter\FilterInterface``.

Filter is a set of operations, calculations, etc., that can be applied to an ``ImageInterface`` instance using ``Filter\FilterInterface::apply()`` method.

Right now only basic filters are available - they simply forward the call to ``ImageInterface`` implementation itself, more filters coming soon...

Architecture
------------

The architecture is very flexible, as the filters don't need any processing logic other than calculating the variables based on some settings and invoking the corresponding method, or sequence of methods, on the ``ImageInterface`` implementation.

The ``Transformation`` object is an example of a composite filter, representing a stack or queue of filters, that get applied to an Image upon application of the ``Transformation`` itself.

.. _Image: /avalanche123/Imagine/blob/master/docs/en/image.rst