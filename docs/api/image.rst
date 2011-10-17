namespace `Imagine\\Image`
==========================

.. php:namespace:: Imagine\Image

.. php:class:: AbstractFont

   .. php:method:: __construct($file, $size, Color $color)

      Constructs :php:class:`Imagine\\Image\\AbstractFont` instance
      with specified $file, $size and $color

      The font size is to be specified in points (e.g. 10pt means 10)

      :param string                $file:  Path to a font file. 
      :param integer               $size:  Font size in points (pts).
      :param Imagine\\Image\\Color $color: Font color.

   .. php:method:: getFile()

      see :php:meth:`Imagine\\Image\\FontInterface::getFile`

   .. php:method:: getSize()

      see :php:meth:`Imagine\\Image\\FontInterface::getSize`

   .. php:method:: getColor()

      see :php:meth:`Imagine\\Image\\FontInterface::getColor`

.. php:interface:: BoxInterface

   .. php:method:: getWidth()

      Gets current image width

      :returns: integer

   .. php:method:: getHeight()

      Gets current image height

      :returns: integer

   .. php:method:: scale($ratio)

      Creates new BoxInterface instance with ratios applied to both sides

      :param float $ratio: Ratio to scale the box to.

      :returns: Imagine\\Image\\BoxInterface

   .. php:method:: increase($size)

      Creates new BoxInterface, adding given size to both sides

      :param integer $size: Number of pixels to increase the box by.

      :returns: Imagine\\Image\\BoxInterface

   .. php:method:: contains(BoxInterface $box, PointInterface $start = null)

      Checks whether current box can fit given box at a given start position,
      start position defaults to top left corner xy(0,0)

      :param Imagine\\Image\\BoxInterface   $box:   Size of the box that we're checking.
      :param Imagine\\Image\\PointInterface $start: Position to see if the box fits at.

      :returns: Boolean

   .. php:method:: square()

      Gets current box square, useful for getting total number of pixels in a
      given box

      :returns: integer

   .. php:method:: __toString()

      Returns a string representation of the current box

      :returns: string

   .. php:method:: widen($width)

      Resizes box to given width, constraining proportions and returns the new box

      :param integer $width: Target width in pixels.

      :returns: Imagine\\Image\\BoxInterface

   .. php:method:: heighten($height)

      Resizes box to given height, constraining proportions and returns the new box

      :param integer $height: Target height in pixels.

      :returns: Imagine\\Image\\BoxInterface

.. php:class:: Box

   .. php:method:: __construct($width, $height)

      Constructs the :php:class:`Imagine\\Image\\Box` with given width and height.

      :param integer $width:  Width of the current box.
      :param integer $height: Height of the current box.

      :throws: Imagine\\Exception\\InvalidArgumentException

   .. php:method:: getWidth()

      see :php:meth:`Imagine\\Image\\BoxInterface::getWidth`

   .. php:method:: getHeight()

      see :php:meth:`Imagine\\Image\\BoxInterface::getHeight`

   .. php:method:: scale($ratio)

      see :php:meth:`Imagine\\Image\\BoxInterface::scale`

   .. php:method:: increase($size)

      see :php:meth:`Imagine\\Image\\BoxInterface::increase`

   .. php:method:: contains(BoxInterface $box, PointInterface $start = null)

      see :php:meth:`Imagine\\Image\\BoxInterface::contains`

   .. php:method:: square()

      see :php:meth:`Imagine\\Image\\BoxInterface::square`

   .. php:method:: __toString()

      see :php:meth:`Imagine\\Image\\BoxInterface::__toString`

   .. php:method:: widen($width)

      see :php:meth:`Imagine\\Image\\BoxInterface::widen`

   .. php:method:: heighten($height)

      see :php:meth:`Imagine\\Image\\BoxInterface::heighten`

.. php:class:: Color

   .. php:method:: __construct($color, $alpha = 0)

      Constructs :php:class:`Imagine\\Image\\Color`, e.g.:
          - ``new Color('fff')`` - will produce non-transparent white color
          - ``new Color('ffffff', 50)`` - will produce 50% transparent white
          - ``new Color(array(255, 255, 255))`` - another way of getting white
          - ``new Color(0x00FF00)`` - hexadecimal notation for green

      :param array|string|integer $color: Color value in one of the allowed formats.
      :param integer              $alpha: Percentage of transparency.

   .. php:method:: getRed()

      Returns RED value of the color

      :returns: integer

   .. php:method:: getGreen()

      Returns GREEN value of the color

      :returns: integer

   .. php:method:: getBlue()

      Returns BLUE value of the color

      :returns: integer

   .. php:method:: getAlpha()

      Returns percentage of transparency of the color.

      :returns: integer

   .. php:method:: dissolve($alpha)

      Returns a copy of current color, incrementing the alpha channel by the
      given amount.

      :param integer $alpha: Percent of tranparency to add.

      :returns: Imagine\\Image\\Color

   .. php:method:: lighten($shade)

      Returns a copy of the current color, lightened by the specified number
      of shades.

      :param integer $shade: Shade to lighten the color by (0 to 127).

      :returns: Imagine\\Image\\Color

   .. php:method:: darken($shade)

      Returns a copy of the current color, darkened by the specified number of
      shades.

      :param integer $shade: Shade to darken the color by (0 to 127).

      :returns: Imagine\\Image\\Color

   .. php:method:: __toString()

      Returns hex representation of the color.

      :returns: string

   .. php:method:: isOpaque()

      Checks if the current color is opaque.

      :returns: Boolean

.. php:interface:: FontInterface

   .. php:method:: getFile()

      Gets the fontfile for current font.

      :returns: string

   .. php:method:: getSize()

      Gets font's integer point size.

      :returns: integer

   .. php:method:: getColor()

      Gets font's color.

      :returns: Imagine\\Image\\Color

   .. php:method:: box($string, $angle = 0)

      Gets BoxInterface of font size on the image based on string and angle.

      :param string  $string: Text to compute the box for.
      :param integer $angle:  Angle to compute the box for.

      :returns: Imagine\\Image\\BoxInterface

.. php:interface:: ImageInterface

   Extends :php:interface:`Imagine\\Image\\ManipulatorInterface`

   .. php:method:: get($format, array $options = array())

      Returns the image content as a binary string.

      :param string $format:  Format of the image (png|gif|jpg).
      :param array  $options: Same options as used for saving.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: string

   .. php:method:: __toString()

      Returns the image content as a PNG binary string

      :param string $format:  Format of the image (png|gif|jpg).
      :param array  $options: Same options as used for saving.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: string

   .. php:method:: draw()

      Instantiates and returns a DrawerInterface instance for image drawing.

      :returns: Imagine\\Draw\\DrawerInterface

   .. php:method:: getSize()

      Returns current image size.

      :returns: Imagine\\Image\\BoxInterface

   .. php:method:: mask()

      Transforms creates a grayscale mask from current image, returns a new
      image, while keeping the existing image unmodified.

      :returns: Imagine\\Image\\ImageInterface

   .. php:method:: histogram()

      Returns array of image colors as Imagine\\Image\\Color instances.

      :returns: array

   .. php:method:: getColorAt(PointInterface $point)

      Returns color at specified positions of current image.

      :param Imagine\\Image\\PointInterface $point: Position to get the color for.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\Color

.. php:interface:: ImagineInterface

   .. php:method:: create(BoxInterface $size, Color $color = null)

      Creates a new empty image with an optional background color.

      :param Imagine\\Image\\BoxInterface $size:  Size of the box of the new image.
      :param Imagine\\Image\\Color        $color: Color to fill the image with.

      :throws: Imagine\\Exception\\InvalidArgumentException
      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ImageInterface

   .. php:method:: open($path)

      Opens an existing image from `$path`.

      :param string $path: Path to the image in the filesystem.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ImageInterface

   .. php:method:: load($string)

      Loads an image from a binary $string.

      :param string $string: Image binary content.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ImageInterface

   .. php:method:: font($file, $size, Color $color)

      Constructs a font with specified `$file`, `$size` and `$color`.

      The font size is to be specified in points (e.g. 10pt means 10).

      :param string              $file:  Font file to use.
      :param integer             $size:  Font size in points (pts).
      :param Imagine\\Image\\Color $color: Font color.

      :returns: Imagine\\Image\\AbstractFont

.. php:interface:: ManipulatorInterface

   .. php:const:: THUMBNAIL_INSET

      Thumbnail generation mode, where the whole image is fit inside a bounding box.

   .. php:const:: THUMBNAIL_OUTBOUND

      Image is resized to fix thumbnail inside and the rest is cropped out.

   .. php:method:: copy()

      Copies current source image into a new ImageInterface instance.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: crop(PointInterface $start, BoxInterface $size)

      Crops a specified box out of the source image (modifies the source image)
      Returns cropped self.

      :param Imagine\\Image\\PointInterface $start: Position to start cropping at.
      :param Imagine\\Image\\BoxInterface   $size:  Size of the area to crop to.

      :throws: Imagine\\Exception\\OutOfBoundsException
      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: resize(BoxInterface $size)

      Resizes current image and returns self.

      :param Imagine\\Image\\BoxInterface $size: Target size.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: rotate($angle, Color $background = null)

      Rotates an image at the given angle., Rotation happens in CW direction.

      Optional $background can be used to specify the fill color of the empty
      area of rotated image.

      :param integer             $angle:      Integer rotation angle value.
      :param Imagine\\Image\\Color $background: Color to fill extra area with.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: paste(ImageInterface $image, PointInterface $start)

      Pastes an image into a parent image.

      Throws exceptions if image exceeds parent image borders or if paste
      operation fails.

      Returns source image.

      :param Imagine\\Image\\ImageInterface $image: Image to paste.
      :param Imagine\\Image\\PointInterface $start: Where to paste the image at.

      :throws: Imagine\\Exception\\InvalidArgumentException
      :throws: Imagine\\Exception\\OutOfBoundsException
      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: save($path, array $options = array())

      Saves the image at a specified path, the target file extension is used
      to determine file format, only jpg, jpeg, gif, png, wbmp and xbm are
      supported.

      :param string $path:    Path to save image to.
      :param array  $options: Options used for saving.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: show($format, array $options = array())

      Outputs the image content.

      :param string $format:  Format of the image, like 'png' or 'jpeg'
      :param array  $options: Array of options to use.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: flipHorizontally()

      Flips current image using horizontal axis.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: flipVertically()

      Flips current image using vertical axis.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: thumbnail(BoxInterface $size, $mode = self::THUMBNAIL_INSET)

      Generates a thumbnail from a current image.

      Returns it as a new image, doesn't modify the current image.

      :param Imagine\\Image\\BoxInterface $size: Target thumbnail size.
      :param string                       $mode: Mode to use.

      :throws: Imagine\\Exception\\RuntimeException

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: applyMask(ImageInterface $mask)

      Applies a given mask to current image's alpha channel.

      :param Imagine\\Image\\ImageInterface $mask: Mask to apply transparency over.

      :returns: Imagine\\Image\\ManipulatorInterface

   .. php:method:: fill(FillInterface $fill)

      Fills image with provided filling, by replacing each pixel's color in
      the current image with corresponding color from FillInterface, and
      returns modified image.

      :param Imagine\\Image\\Fill\\FillInterface $fill: Type of fill to apply.

      :returns: Imagine\\Image\\ManipulatorInterface

.. php:class:: Point

   .. php:method:: __construct($x, $y)

      Constructs :php:class:`Imagine\\Image\\Point`.

      :param integer $x: Horizontal position.
      :param integer $y: Vertical position.

      :throws: Imagine\\Exception\\InvalidArgumentException

   .. php:method:: getX()

      see :php:meth:`Imagine\\Image\\PointInterface::getX`

   .. php:method:: getY()

      see :php:meth:`Imagine\\Image\\PointInterface::getY`

   .. php:method:: in(BoxInterface $box)

      see :php:meth:`Imagine\\Image\\PointInterface::in`

   .. php:method:: move($amount)

      see :php:meth:`Imagine\\Image\\PointInterface::move`

   .. php:method:: __toString()

      see :php:meth:`Imagine\\Image\\PointInterface::__toString`


.. php:interface:: PointInterface

   .. php:method:: getX()

      Gets points x coordinate

      :returns: integer

   .. php:method:: getY()

      Gets points y coordinate

      :returns: integer

   .. php:method:: in(BoxInterface $box)

      Checks if current coordinate is inside a given bo

      :param Imagine\\Image\\BoxInterface $box: The box to check against.

      :returns: Boolean

   .. php:method:: move($amout)

      Returns another point, moved by a given amout from current coordinates

      :param integer $amout: Amount to move the point by.

      :returns: Imagine\\Image\\ImageInterface

   .. php:method:: __toString()

      Gets a string representation for the current point

      :returns: string

namespace `Imagine\\Image\\Fill`
--------------------------------

.. php:namespace:: Imagine\Image\Fill

.. php:interface:: FillInterface

   ..php:method:: getColor(PointInterface $position)

   Gets color of the fill for the given position.

   :param Imagine\\Image\\PointInterface $position: Coordinate to get the color for.

   :returns: Imagine\\Image\\Color

namespace `Imagine\\Image\\Fill\\Gradient`
++++++++++++++++++++++++++++++++++++++++++

.. php:namespace:: Imagine\Image\Fill\Gradient

.. php:class:: Horizontal

   .. php:method:: getDistance(PointInterface $position)

      see :php:meth:`Imagine\\Mask\\Gradient\\Linear::getDistance`

.. php:class:: Linear

   .. php:method:: __construct($length, Color $start, Color $end)

      Constructs a linear gradient with overall gradient length, and start and
      end shades, which default to 0 and 255 accordingly

      :param integer               $length: Length of the fill.
      :param Imagine\\Image\\Color $start:  Starting color.
      :param Imagine\\Image\\Color $end:    Color to move to.

   .. php:method:: getColor(PointInterface $position)

      see :php:meth:`Imagine\\Image\\Fill\\FillInterface::getShade`

   .. php:method:: getStart()

      :returns: Imagine\\Image\\Color

   .. php:method:: getEnd()

      :returns: Imagine\\Image\\Color

   .. php:method:: getDistance(PointInterface $position);

      Get the distance of the position relative to the beginning of the gradient

      :param Imagine\\Image\\PointInterface $position: Position to get the color for.

      :returns: integer

.. php:class:: Vertical

   .. php:method:: getDistance(PointInterface $position)

      see :php:meth:`Imagine\\Mask\\Gradient\\Linear::getDistance`

namespace `Imagine\\Image\\Point`
---------------------------------

.. php:namespace:: Imagine\Image\Point

.. php:class:: Center

   .. php:method:: __construct(BoxInterface $box)

      Constructs :php:class:`Imagine\\Image\\Point\\Center` with size instance,
      it needs to be relative to.

      :param Imagine\\Image\\BoxInterface $size: Box to get center for.

   .. php:method:: getX()

      see :php:meth:`Imagine\\Image\\PointInterface::getX`

   .. php:method:: getY()

      see :php:meth:`Imagine\\Image\\PointInterface::getY`

   .. php:method:: in(BoxInterface $box)

      see :php:meth:`Imagine\\Image\\PointInterface::in`

   .. php:method:: move($amount)

      see :php:meth:`Imagine\\Image\\PointInterface::move`

   .. php:method:: __toString()

      see :php:meth:`Imagine\\Image\\PointInterface::__toString`
