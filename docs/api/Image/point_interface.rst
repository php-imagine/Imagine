.. php:namespace:: Imagine\Image

.. php:interface:: PointInterface

   .. php:method:: PointInterface::getX()

      Gets points x coordinate

      :returns: integer

   .. php:method:: PointInterface::getY()

      Gets points y coordinate

      :returns: integer

   .. php:method:: PointInterface::in()

      Checks if current coordinate is inside a given bo

      :param Imagine\\Image\\BoxInterface $box:

      :returns: Boolean

   .. php:method:: PointInterface::move()

      Returns another point, moved by a given amout from current coordinates

      :param $amout:

      :returns: Imagine\\Image\\ImageInterface

   .. php:method:: PointInterface::__toString()

      Gets a string representation for the current point

      :returns: string