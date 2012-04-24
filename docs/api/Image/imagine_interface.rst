.. php:namespace:: Imagine\Image

.. php:interface:: ImagineInterface

   .. php:const:: ImagineInterface:: RESOLUTION_PIXELSPERINCH       = 'ppi';

   .. php:const:: ImagineInterface:: RESOLUTION_PIXELSPERCENTIMETER = 'ppc';

   .. php:method:: ImagineInterface::create()

      Creates a new empty image with an optional background color

      :param Imagine\\Image\\BoxInterface $size:
      :param Imagine\\Image\\Color $color:

      :throws: Imagine\\Exception\\InvalidArgumentException

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ImageInterface

   .. php:method:: ImagineInterface::open()

      Opens an existing image from $path

      :param string $path:

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ImageInterface

   .. php:method:: ImagineInterface::load()

      Loads an image from a binary $string

      :param string $string:

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ImageInterface

   .. php:method:: ImagineInterface::read()

      Loads an image from a resource $resource

      :param resource $resource:

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ImageInterface

   .. php:method:: ImagineInterface::font()

      Constructs a font with specified $file, $size and $color

      The font size is to be specified in points (e.g. 10pt means 10)

      :param string $file:
      :param integer $size:
      :param Imagine\\Image\\Color $color:

      :returns: Imagine\\Image\\AbstractFont