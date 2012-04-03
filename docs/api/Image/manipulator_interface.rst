.. php:namespace:: Imagine\Image

.. php:interface:: ManipulatorInterface

   .. php:const:: ManipulatorInterface:: THUMBNAIL_INSET    = 'inset';

   .. php:const:: ManipulatorInterface:: THUMBNAIL_OUTBOUND = 'outbound';

   .. php:method:: ManipulatorInterface::copy()

      Copies current source image into a new ImageInterface instance

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: ManipulatorInterface::crop()

      Crops a specified box out of the source image (modifies the source image)
      Returns cropped self

      :param Imagine\\Image\\PointInterface $start:
      :param Imagine\\Image\\BoxInterface $size:

      :throws: Imagine\\Exception\\OutOfBoundsException

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: ManipulatorInterface::resize()

      Resizes current image and returns self

      :param Imagine\\Image\\BoxInterface $size:

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: ManipulatorInterface::rotate()

      Rotates an image at the given angle.
      Optional $background can be used to specify the fill color of the empty
      area of rotated image.

      :param integer $angle:
      :param Imagine\\Image\\Color $background:

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: ManipulatorInterface::paste()

      Pastes an image into a parent image
      Throws exceptions if image exceeds parent image borders or if paste
      operation fails

      Returns source image

      :param Imagine\\Image\\ImageInterface $image:
      :param Imagine\\Image\\PointInterface $start:

      :throws: Imagine\\Exception\\InvalidArgumentException

      :throws: Imagine\\Exception\\OutOfBoundsException

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: ManipulatorInterface::save()

      Saves the image at a specified path, the target file extension is used
      to determine file format, only jpg, jpeg, gif, png, wbmp and xbm are
      supported

      :param string $path:
      :param array $options:

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: ManipulatorInterface::show()

      Outputs the image content

      :param string $format:
      :param array $options:

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: ManipulatorInterface::flipHorizontally()

      Flips current image using horizontal axis

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: ManipulatorInterface::flipVertically()

      Flips current image using vertical axis

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: ManipulatorInterface::thumbnail()

   .. php:method:: ManipulatorInterface::strip()

      Removes Profiles and Comments from the image

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: ManipulatorInterface::thumbnail()

      Generates a thumbnail from a current image
      Returns it as a new image, doesn't modify the current image

      :param Imagine\\Image\\BoxInterface $size:
      :param string $mode:

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: ManipulatorInterface::applyMask()

      Applies a given mask to current image's alpha channel

      :param Imagine\\Image\\ImageInterface $mask:

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: ManipulatorInterface::fill()

      Fills image with provided filling, by replacing each pixel's color in
      the current image with corresponding color from FillInterface, and
      returns modified image

      :param Imagine\\Image\\Fill\\FillInterface $fill:

      :returns: Imagine\\Image\\ManipulatorInterface