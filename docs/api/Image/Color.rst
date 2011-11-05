.. php:namespace:: Imagine\Image

.. php:class:: Color

   .. php:method:: Color::__construct()

      Constructs image color, e.g.:
          - new Color('fff') - will produce non-transparent white color
          - new Color('ffffff', 50) - will product 50% transparent white
          - new Color(array(255, 255, 255)) - another way of getting white
          - new Color(0x00FF00) - hexadecimal notation for green

      :param array|string|integer $color:
      :param integer $alpha:

   .. php:method:: Color::getRed()

      Returns RED value of the color

      :returns: integer

   .. php:method:: Color::getGreen()

      Returns GREEN value of the color

      :returns: integer

   .. php:method:: Color::getBlue()

      Returns BLUE value of the color

      :returns: integer

   .. php:method:: Color::getAlpha()

      Returns percentage of transparency of the color

      :returns: integer

   .. php:method:: Color::dissolve()

      Returns a copy of current color, incrementing the alpha channel by the
      given amount

      :param integer $alpha:

      :returns: Imagine\\Image\\Color

   .. php:method:: Color::lighten()

      Returns a copy of the current color, lightened by the specified number
      of shades

      :param integer $shade:

      :returns: Imagine\\Image\\Color

   .. php:method:: Color::darken()

      Returns a copy of the current color, darkened by the specified number of
      shades

      :param integer $shade:

      :returns: Imagine\\Image\\Color

   .. php:method:: Color::__toString()

      Returns hex representation of the color

      :returns: string

   .. php:method:: Color::isOpaque()

      Checks if the current color is opaque

      :returns: Boolean