namespace `Imagine\\Draw`
===========================

.. php:namespace:: Imagine\Draw

.. php:class:: DrawerInterface

   Instance of this interface is returned by :php:meth:`Imagine\\Image\\ImageInterface::draw`.

   .. php:method:: arc(PointInterface $center, BoxInterface $size, $start, $end, Color $color)

      Draws an arc on a starting at a given x, y coordinates under a given start and end angles

      :param Imagine\\Image\\PointInterface $center: Center of the arc.
      :param Imagine\\Image\\BoxInterface   $size:   Size of the bounding box.
      :param integer                        $start:  Start angle.
      :param integer                        $end:    End angle.
      :param Imagine\\Image\\Color          $color:  Line color.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Draw\\DrawerInterface

   .. php:method:: chord(PointInterface $center, BoxInterface $size, $start, $end, Color $color)

      Same as :php:meth:`Imagine\\Draw\\DrawerInterface::arc`, but also connects end points with a straight line

      :param Imagine\\Image\\PointInterface $center: Center of the chord.
      :param Imagine\\Image\\BoxInterface   $size:   Size of the bounding box.
      :param integer                        $start:  Start angle.
      :param integer                        $end:    End angle.
      :param Imagine\\Image\\Color          $color:  Line color.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Draw\\DrawerInterface

   .. php:method:: ellipse(PointInterface $center, BoxInterface $size, Color $color, $fill = false)

      Draws and ellipse with center at the given x, y coordinates, and given width and height

      :param Imagine\\Image\\PointInterface $center: Center of the ellipse.
      :param Imagine\\Image\\BoxInterface   $size:   Size of the bounding box.
      :param Imagine\\Image\\Color          $color:  Color to be used for line and fill.
      :param Boolean                        $fill:   Whether to fill the ellipse of not.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Draw\\DrawerInterface

   .. php:method:: line(PointInterface $start, PointInterface $end, Color $outline)

      Draws a line from start(x, y) to end(x, y) coordinates

      :param Imagine\\Image\\PointInterface $start:   Start coordinate of the line.
      :param Imagine\\Image\\PointInterface $end:     End coordinate of the line.
      :param Imagine\\Image\\Color          $outline: Color of the line.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Draw\\DrawerInterface

   .. php:method:: pieSlice(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $fill = false)

      Same as :php:meth:`Imagine\\Draw\\DrawerInterface::arc`, but connects end points and the center

      :param Imagine\\Image\\PointInterface $center: Center of the chord.
      :param Imagine\\Image\\BoxInterface   $size:   Size of the bounding box.
      :param integer                        $start:  Start angle.
      :param integer                        $end:    End angle.
      :param Imagine\\Image\\Color          $color:  Line color.
      :param Boolean                        $fill:   Whether to fill the slice of not.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Draw\\DrawerInterface

   .. php:method:: dot(PointInterface $position, Color $color)

      Places a one pixel point at specific coordinates and fills it with specified color

      :param Imagine\\Image\\PointInterface $position: Coordinate of the dot.
      :param Imagine\\Image\\Color          $color:    Color of the dot.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Draw\\DrawerInterface

   .. php:method:: polygon(array $coordinates, Color $color, $fill = false)

      Draws a polygon using array of x, y coordinates. Must contain at least three coordinates

      :param array                 $coordinates: Array of coordinates of every angle.
      :param Imagine\\Image\\Color $color:       Color of the outline and fill.
      :param Boolean               $fill:        Whether to fill the polygon or not.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Draw\\DrawerInterface

   .. php:method:: text($string, AbstractFont $font, PointInterface $position, $angle = 0)

      Annotates image with specified text at a given position starting on the top left of the final text box

      The rotation is done CW

      :param string                         $string:   Text for annotation.
      :param Imagine\\Image\\AbstractFont   $font:     Font instance to use.
      :param Imagine\\Image\\PointInterface $position: Top left coordinate of annotation.
      :param integer                        $angle:    Rotation angle.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Draw\\DrawerInterface
