Layers manipulation
===================

``ImageInterface`` provides an access for multi-layers image such as PSD files
or animated gif.
By calling the ``layers()`` method, you will get an iterable layer collection
implementing the ``LayersInterface``. As you will see, a layer implements
``ImageInterface``

Disclaimer
----------

Imagine is a fluent API to use Imagick, Gmagick or GD driver. These drivers
do not handle all multi-layers formats equally. For example :

 * PSD format should be flatten before being saved. (libraries would split it
 into different files),
 * animated gif must not be flatten otherwise the animation would be lost.
 * Tiff files should be split in multiple files or the result might be a pile
 of HD and thumbnail
 * GD does not support layers.

You have to run tests against the formats you are using and their support by
the driver you want before deploying in production.

Animated gif frame manipulation
-------------------------------

The following example extract all frames of the cats.gif file :

.. code-block:: php

    <?php

    $i = 0;
    foreach ($imagine->open('cats.gif')->layers() as $layer) {
        $layer->save("frame-$i.png");
        $i++;
    }

This one adds some text on frames :

.. code-block:: php

    <?php

    $image = $imagine->open('cats.gif');
    $i = 0;
    foreach ($image->layers() as $layer) {
        $layer->draw()
              ->text($i, new Font('coolfont.ttf', 12, new Color('white')), new Point(10, 10));
        $i++;
    }

    // save modified animation
    $image->save('cats-modified.gif', array('flatten' => 'false'));

.. NOTE::
    Full support for animated image is planned in the nearest future.
