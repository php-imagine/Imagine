.. php:namespace:: Imagine\Image

.. php:interface:: FontInterface

   .. php:method:: FontInterface::getFile()

      Gets the fontfile for current font

      :returns: string

   .. php:method:: FontInterface::getSize()

      Gets font's integer point size

      :returns: integer

   .. php:method:: FontInterface::getColor()

      Gets font's color

      :returns: Imagine\\Image\\Color

   .. php:method:: FontInterface::box()

      Gets BoxInterface of font size on the image based on string and angle

      :param string $string:
      :param integer $angle:

      :returns: Imagine\\Image\\BoxInterface