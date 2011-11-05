.. php:namespace:: Imagine\Image\Fill\Gradient

.. php:class:: Linear

   .. php:method:: Linear::__construct()

      Constructs a linear gradient with overall gradient length, and start and
      end shades, which default to 0 and 255 accordingly

      :param integer $length:
      :param Imagine\\Image\\Color $start:
      :param Imagine\\Image\\Color $end:

   .. php:method:: Linear::getColor()

      (non-PHPdoc)

      :see: Imagine\\Image\\Fill\\FillInterface::getShade()

   .. php:method:: Linear::getStart()

      :returns: Imagine\\Image\\Color

   .. php:method:: Linear::getEnd()

      :returns: Imagine\\Image\\Color

   .. php:method:: Linear::getDistance()

      Get the distance of the position relative to the beginning of the gradient

      :param Imagine\\Image\\PointInterface $position:

      :returns: integer