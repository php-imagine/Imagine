Imagine API
===========

``Imagine\ImagineInterface`` and its implementations is the main entry point into ``Imagine``. You may think of it as a factory for complex object in ``Imagine``. It is responsible for creating and opening ``Imagine\ImageInterface`` instances and for instantiating ``Imagine\Image\AbstractFont`` object.

Available Methods
-----------------

* ``->create(Imagine\Image\BoxInterface $size, Imagine\Image\Color $color = null)`` - creates an new ``Imagine\ImageInterface`` instance with the given background ``$color`` (opaque white will be used if nothing is specified)

* ``->open($path)`` - open an image at the given path and returns ``Imagine\ImageInterface``, representing it

* ``->load($string)`` - loads image from binary string and returns ``Imagine\ImageInterface``

* ``->font($file, $size, Imagine\Image\Color $color)`` - returns ``Imagine\Image\AbstractFont`` of the given ``$size`` from the given ``$path`` and of a given ``Imagine\Image\Color``