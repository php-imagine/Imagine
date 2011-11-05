.. php:namespace:: Imagine\Image

.. php:interface:: ImageInterface

   .. php:method:: ImageInterface::get()

      Returns the image content as a binary string

      :param string $format:
      :param array $options:

      :throws: Imagine\\Exception\\RuntimeException

      :returns: string $inary

   .. php:method:: ImageInterface::__toString()

      Returns the image content as a PNG binary string

      :param string $format:
      :param array $options:

      :throws: Imagine\\Exception\\RuntimeException

      :returns: string $inary

   .. php:method:: ImageInterface::draw()

      Instantiates and returns a DrawerInterface instance for image drawing

      :returns: Imagine\\Draw\\DrawerInterface

   .. php:method:: ImageInterface::getSize()

      Returns current image size

      :returns: Imagine\\Image\\BoxInterface

   .. php:method:: ImageInterface::mask()

      Transforms creates a grayscale mask from current image, returns a new
      image, while keeping the existing image unmodified

      :returns: Imagine\\Image\\ImageInterface

   .. php:method:: ImageInterface::histogram()

      Returns array of image colors as Imagine\\Image\\Color instances

      :returns: array

   .. php:method:: ImageInterface::getColorAt()

      Returns color at specified positions of current image

      :param Imagine\\Image\\PointInterface $point:

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\Color