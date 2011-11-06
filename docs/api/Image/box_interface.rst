.. php:namespace:: Imagine\Image

.. php:interface:: BoxInterface

   .. php:method:: BoxInterface::getHeight()

      Gets current image height

      :returns: integer

   .. php:method:: BoxInterface::getWidth()

      Gets current image width

      :returns: integer

   .. php:method:: BoxInterface::scale()

      Creates new BoxInterface instance with ratios applied to both sides

      :param float $ratio:

      :returns: Imagine\\Image\\BoxInterface

   .. php:method:: BoxInterface::increase()

      Creates new BoxInterface, adding given size to both sides

      :param integer $size:

      :returns: Imagine\\Image\\BoxInterface

   .. php:method:: BoxInterface::contains()

      Checks whether current box can fit given box at a given start position,
      start position defaults to top left corner xy(0,0)

      :param Imagine\\Image\\BoxInterface $box:
      :param Imagine\\Image\\PointInterface $start:

      :returns: Boolean

   .. php:method:: BoxInterface::square()

      Gets current box square, useful for getting total number of pixels in a
      given box

      :returns: integer

   .. php:method:: BoxInterface::__toString()

      Returns a string representation of the current box

      :returns: string

   .. php:method:: BoxInterface::widen()

      Resizes box to given width, constraining proportions and returns the new box

      :param integer $width:

      :returns: Imagine\\Image\\BoxInterface

   .. php:method:: BoxInterface::heighten()

      Resizes box to given height, constraining proportions and returns the new box

      :param integer $height:

      :returns: Imagine\\Image\\BoxInterface