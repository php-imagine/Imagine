Image API
=========

The main piece of image processing functionality is concentrated in the ``ImageInterface`` implementations (one per driver - e.g. ``Imagick\Image``)

The main idea of Imagine is to avoid driver specific methods spill outside of this class and couple of other internal interfaces (``Draw\DrawerInterface``), so that the filters and any other image manipulations can operate on ``ImageInterface`` through its public API.

Available Methods
-----------------

* ``->copy()`` - duplicates current image and returns a new ImageInterface instance

* ``->crop(PointInterface $start, $width, $height`)` - crops the image, starting at the ``$start`` coordinates and extending to the specified ``$width`` and ``$height``

* ``->flipHorizontally()`` - creates a horizontal mirror reflection of image

* ``->flipVertically()`` - creates a vertical mirror reflection of image

* ``->paste(ImageInterface $image, PointInterface $start)`` - pastes another ``$image`` into the source image at the ``$start`` position

* ``->resize($width, $height)`` - resizes image to given ``$height`` and ``$width`` exactly

* ``->rotate($angle, Color $background = null)`` - rotates the image clockwise by the given ``$angle``, or counter-clockwise if the angle is negative. If a background ``$color`` is given, it will be used to fill empty parts of the image (white will be used by default).

* ``->save($path, array $options = array())`` - saves current image to the specified ``$path``. The target file extension will be used to infer the output format. For 'jpeg/jpg' and 'png' images, a 'quality' option of 0-100 is accepted. 'png' images also accept a 'filter' option (consult the GD manual for more information). For 'wbmp' or 'xbm' images, a 'foreground' option may be specified.

* ``->show($format, array $options = array())`` - outputs image content in the given format, allowing the same options as the `save()` method

* ``->thumbnail($width, $height, $mode = self::THUMBNAIL_INSET)`` - prepares an image thumbnail, based on the target dimensions, while preserving proportions. The thumbnail operation returns a new ``ImageInterface`` instance that is a processed copy of the original (the source image is not modified). If thumbnail mode is ``ImageInterface::THUMBNAIL_INSET``, the original image is scaled down so it is fully contained within the thumbnail dimensions. The specified ``$width`` and ``$height`` will be considered maximum limits. Unless the given dimensions are equal to the original image's aspect ratio, one dimension in the resulting thumbnail will be smaller than the given limit. If ``ImageInterface::THUMBNAIL_OUTBOUND`` mode is chosen, then the thumbnail is scaled so that its smallest side equals the length of the corresponding side in the original image. Any excess outside of the scaled thumbnail's area will be cropped, and the returned thumbnail will have the exact ``$width`` and ``$height`` specified.

* ``->getSize()`` - returns ``Imagine\Image\BoxInterface`` instance, that represents current image's size

* ``->get($format, array $options = array())`` - gets image binary as string

* ``->__toString()`` - returns image string in ``png`` format

* ``->applyMask(ImageInterface $mask)`` - applies a grayscale image as a transparency mask to current image

* ``->mask()`` - returns a masked copy of current image - masks are grayscale and don't have any transparency

* ``->fill(FillInterface $fill)`` - applies a fill to every pixel in the image, this is rather expensive in performace, as it iterates through every pixel on the image, but gives control to fine-tune the image fill

* ``->histogram()`` - returns array of unique ``Image\Color`` instances, that consitute current ``ImageInterface`` instance