Introduction
============

Basic usage
-----------

Open Existing Images
++++++++++++++++++++

To open an existing image, all you need is to instantiate an image factory and invoke ``ImagineInterface::open()`` with ``$path`` to image as the  argument

::

    <?php
    $imagine = new Imagine\Gd\Imagine();
    // or
    $imagine = new Imagine\Imagick\Imagine();
    
    $image = $imagine->open('/path/to/image.jpg');

.. TIP::
    Read more about ImagineInterface_

The ``ImagineInterface::open()`` method may throw one of the following exceptions:

* ``Imagine\Exception\InvalidArgumentException``
* ``Imagine\Exception\RuntimeException``

.. TIP::
    Read more about exceptions_

Now that you've opened an image, you can perform manipulations on it:

::

    <?php
    use Imagine\Image\Box;
    use Imagine\Image\Point;
    
    $image->resize(new Box(15, 25))
        ->rotate(45)
        ->crop(new Point(0, 0), new Box(45, 45))
        ->save('/path/to/new/image.jpg');

.. TIP::
    Read more about ImageInterface_
    Read more about coordinates_

Create New Images
+++++++++++++++++

Imagine also lets you create new, empty images. The following example creates an empty image of width 400px and height 300px:

::

    <?php
    $image = $imagine->create(new Imagine\Image\Box(400, 300));

You can optionally specify the fill color for the new image, which defaults to opaque white. The following example creates a new image with a fully-transparent black background:

::

    <?php
    $image = $imagine->create(new Imagine\Image\Box(400, 300), new Imagine\Image\Color('000', 100));

Color Class
+++++++++++

Color is a class in Imagine, which takes two arguments in its constructor: the RGB color code and a transparency percentage. The following examples are equivalent ways of defining a fully-transparent white color.

::

    <?php
    $white = new Imagine\Image\Color('fff', 100);
    $white = new Imagine\Image\Color('ffffff', 100);
    $white = new Imagine\Image\Color('#fff', 100);
    $white = new Imagine\Image\Color('#ffffff', 100);
    $white = new Imagine\Image\Color(array(255, 255, 255), 100);

After you have instantiated a color, you can easily get its Red, Green, Blue and Alpha (transparency) values:

::

    <?php
    var_dump(array(
        'R' => $white->getRed(),
        'G' => $white->getGreen(),
        'B' => $white->getBlue(),
        'A' => $white->getAlpha()
    ));

Advanced Example - An Image Collage
-----------------------------------

Assume we were given the not-so-easy task of creating a four-by-four collage of 16 student portraits for a school yearbook.  Each photo is 30x40 px and we need four rows and columns in our collage, so the final product will be 120x160 px.

Here is how we would approach this problem with Imagine.

::

    <?php
    use Imagine;
    
    // make an empty image (canvas) 120x160px
    $collage = $imagine->create(new Imagine\Image\Box(120, 160));
    
    // starting coordinates (in pixels) for inserting the first image
    $x = 0;
    $y = 0;
    
    foreach (glob('/path/to/people/photos/*.jpg') as $path) {
        // open photo
        $photo = $imagine->open($path);
        
        // paste photo at current position
        $collage->paste($photo, new Imagine\Image\Point($x, $y));
        
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

Architecture
------------

The architecture is very flexible, as the filters don't need any processing logic other than calculating the variables based on some settings and invoking the corresponding method, or sequence of methods, on the ``ImageInterface`` implementation.

The ``Transformation`` object is an example of a composite filter, representing a stack or queue of filters, that get applied to an Image upon application of the ``Transformation`` itself.

.. _ImagineInterface: /avalanche123/Imagine/blob/master/docs/en/imagine.rst
.. _ImageInterface: /avalanche123/Imagine/blob/master/docs/en/image.rst
.. _coordinates: /avalanche123/Imagine/blob/master/docs/en/coordinates.rst
.. _exceptions: /avalanche123/Imagine/blob/master/docs/en/exceptions.rst