Image filters and delayed processing
====================================

``ImageInterface`` in Imagine is very powerful and provides all the basic transformations that you might need, however, sometimes it might be useful to be able to group all of them into a dedicated object, that will know which transformations, in which sequence and with which parameters to invoke on the ``ImageInterface`` instance. For that, Imagine provides ``FilterInterface`` and some basic filters that call transformation on the ``ImageInterface`` directly as an example.

.. NOTE::
    more filters and advanced transformations is planned in the nearest future

Image Transformations, aka Lazy Processing
------------------------------------------

Sometimes we're not comfortable with opening an image inline, and would like to apply some pre-defined operations in the lazy manner.

For that, Imagine provides so-called image transformations.

Image transformation is implemented via the ``Filter\Transformation`` class, which mostly conforms to ``ImageInterface`` and can be used interchangeably with it. The main difference is that transformations may be stacked and performed on a real ``ImageInterface`` instance later using the ``Transformation::apply()`` method.

Example of a naive thumbnail implementation:

.. code-block:: php

    <?php

    $transformation = new Imagine\Filter\Transformation();
    
    $transformation->thumbnail(new Imagine\Image\Box(30, 30))
        ->save('/path/to/resized/thumbnail.jpg');
    
    $transformation->apply($imagine->open('/path/to/image.jpg'));

The result of ``apply()`` is the modified image instance itself, so if we wanted to create a mass-processing thumbnail script, we would do something like the following:

.. code-block:: php

    <?php

    $transformation = new Imagine\Filter\Transformation();
    
    $transformation->thumbnail(new Imagine\Image\Box(30, 30));
    
    foreach (glob('/path/to/lots/of/images/*.jpg') as $path) {
        $transformation->apply($imagine->open($path))
            ->save('/path/to/resized/'.md5($path).'.jpg');
    }

The ``Filter\Transformation`` class itself is simply a very specific implementation of ``FilterInterface``, which is a more generic interface, that let's you pre-define certain operations and variable calculations and apply them to an ``ImageInterface`` instance later.

Filters
-------

As we already know, ``Filter\Transformation`` is just a very special case of ``Filter\FilterInterface``.

Filter is a set of operations, calculations, etc., that can be applied to an ``ImageInterface`` instance using ``Filter\FilterInterface::apply()`` method.

Right now only basic filters are available - they simply forward the call to ``ImageInterface`` implementation itself, more filters coming soon...

