.. php:namespace:: Imagine\Image

.. php:class:: AbstractFont

   .. php:attr:: $file

      :var: string

   .. php:attr:: $size

      :var: integer

   .. php:attr:: $color

      :var: Imagine\\Image\\Color

   .. php:method:: AbstractFont::__construct()

      Constructs a font with specified $file, $size and $color

      The font size is to be specified in points (e.g. 10pt means 10)

      :param string $file:
      :param integer $size:
      :param Imagine\\Image\\Color $color:

   .. php:method:: AbstractFont::getFile()

      (non-PHPdoc)

      :see: Imagine\\Image\\FontInterface::getFile()

   .. php:method:: AbstractFont::getSize()

      (non-PHPdoc)

      :see: Imagine\\Image\\FontInterface::getSize()

   .. php:method:: AbstractFont::getColor()

      (non-PHPdoc)

      :see: Imagine\\Image\\FontInterface::getColor()