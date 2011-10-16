Opening or creating images
==========================

``Imagine\Image\ImagineInterface`` and its implementations is the main entry point into Imagine. You may think of it as a factory for ``Imagine\Image\ImageInterface`` as it is responsible for creating and opening instances of it and also for instantiating ``Imagine\Image\FontInterface`` object.

Available Methods
-----------------

* ``->create(Imagine\Image\BoxInterface $size, Imagine\Image\Color $color = null)`` - creates an new ``Imagine\Image\ImageInterface`` instance with the given background ``$color`` (opaque white will be used if nothing is specified)

* ``->open($path)`` - open an image at the given path and returns ``Imagine\Image\ImageInterface``, representing it

* ``->load($string)`` - loads image from binary string and returns ``Imagine\Image\ImageInterface``

* ``->font($file, $size, Imagine\Image\Color $color)`` - returns ``Imagine\Image\AbstractFont`` of the given ``$size`` from the given ``$path`` and of a given ``Imagine\Image\Color``