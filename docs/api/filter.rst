namespace `Imagine\\Filter`
===========================

.. php:namespace:: Imagine\Filter

.. php:class:: ImagineAware

   .. php:method:: setImagine(ImagineInterface $imagine)

      Sets :php:interface:`Imagine\\Image\\ImagineInterface` instance.

      :param Imagine\\Image\\ImagineInterface $imagine: Imagine instance to set.

      :returns: void

   .. php:method:: getImagine()

      Gets :php:interface:`Imagine\\Image\\ImagineInterface` instance.

      :throws: Imagine\\Exception\\InvalidArgumentException

      :returns: Imagine\\Image\\ImagineInterface

.. php:interface:: FilterInterface

   Imagine Filter interface.

   .. php:method:: apply(ImageInterface $image)

      Applies scheduled transformation to ImageInterface instance

      Returns processed ImageInterface instance

      :param Imagine\\Image\\ImageInterface $image: Image to apply the filter to.

      :returns: Imagine\\Image\\ImageInterface

.. php:class:: Transformation

   Implements :php:interface:`Imagine\\Image\\ManipulatorInterface`.

   Lets you operate on it just like on a regular image, without doing eager processing.

   Lets you apply it to an :php:interface:`Imagine\\Image\\ImageInterface` instance later
   and repeats all of it opertations.

   .. php:method:: __construct(ImagineInterface $imagine = null)

      Constructs :php:class:`Imagine\\Filter\\Transformtaion` instance

      :param Imagine\\Image\\ImagineInterface $imagine: An ImagineInterface instance

   .. php:method:: applyFilter(ImageInterface $image, FilterInterface $filter)

      Applies a given FilterInterface onto given ImageInterface and returns
      modified ImageInterface

      :param Imagine\\Filter\\FilterInterface: $filter
      :param Imagine\\Image\\ImageInterface:   $image

      :returns: Imagine\\Image\\ImageInterface
      :throws: Imagine\\Exception\\InvalidArgumentException

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`

   .. php:method:: copy()

      see :php:meth:`Imagine\\Image\\ManipulatorInterface::copy`

   .. php:method:: crop(PointInterface $start, BoxInterface $size)

      see :php:meth:`Imagine\\Image\\ManipulatorInterface::crop`

   .. php:method:: flipHorizontally()

      see :php:meth:`Imagine\\Image\\ManipulatorInterface::flipHorizontally`

   .. php:method:: flipVertically()

      see :php:meth:`Imagine\\Image\\ManipulatorInterface::flipVertically`

   .. php:method:: paste(ImageInterface $image, PointInterface $start)

      see :php:meth:`Imagine\\Image\\ManipulatorInterface::paste`

   .. php:method:: applyMask(ImageInterface $mask)

      see :php:meth:`Imagine\\Image\\ManipulatorInterface::applyMask`

   .. php:method:: fill(FillInterface $fill)

      see :php:meth:`Imagine\\Image\\ManipulatorInterface::fill`

   .. php:method:: resize(BoxInterface $size)

      see :php:meth:`Imagine\\Image\\ManipulatorInterface::resize`

   .. php:method:: rotate($angle, Color $background = null)

      see :php:meth:`Imagine\\Image\\ManipulatorInterface::rotate`

   .. php:method:: save($path, array $options = array())

      see :php:meth:`Imagine\\Image\\ManipulatorInterface::save`

   .. php:method:: show($format, array $options = array())

      see :php:meth:`Imagine\\Image\\ManipulatorInterface::show`

   .. php:method:: thumbnail(BoxInterface $size, $mode = ImageInterface::THUMBNAIL_INSET)

      see :php:meth:`Imagine\\Image\\ManipulatorInterface::thumbnail`

   .. php:method:: add(FilterInterface $filter)

      Registers a given FilterInterface in an internal array of filters for
      later application to an instance of ImageInterface

      :param Imagine\\Filter\\FilterInterface $filter: Filter to add to filters stack.

      :returns: Imagine\\Filter\\Transformation

namespace `Imagine\\Filter\\Basic`
----------------------------------

.. php:namespace:: Imagine\Filter\Basic

.. php:class:: ApplyMask

   .. php:method:: __construct(ImageInterface $mask)

      Constructs :php:class:`Imagine\\Filter\\Basic\\ApplyMask` instance

      :param Imagine\\Image\\ImageInterface $mask: Mask to apply to image.

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`

.. php:class:: Copy

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`

.. php:class:: Crop

   .. php:method:: __construct(PointInterface $start, BoxInterface $size)

      Constructs :php:class:`Imagine\\Filter\\Basic\\Crop` instance

      :param Imagine\\Image\\PointInterface $start: Coordinates to start cropping from.
      :param Imagine\\Image\\BoxInterface   $size:  Size of the area to crop.

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`

.. php:class:: Fill

   .. php:method:: __construct(FillInterface $fill)

      Constructs :php:class:`Imagine\\Filter\\Basic\\Fill` instance

      :param Imagine\\Image\\Fill\\FillInterface $fill: Fill to apply.

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`

.. php:class:: FlipHorizontally

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`

.. php:class:: FlipVertically

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`

.. php:class:: Paste

   .. php:method:: __construct(ImageInterface $image, PointInterface $start)

      Constructs :php:class:`Imagine\\Filter\\Basic\\Paste` instance

      :param Imagine\\Image\\ImageInterface $image: Image to paste.
      :param Imagine\\Image\\PointInterface $start: Position to paste image at.

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`

.. php:class:: Resize

   .. php:method:: __construct(BoxInterface $size)

      Constructs :php:class:`Imagine\\Filter\\Basic\\Resize` instance

      :param Imagine\\Image\\BoxInterface $size: Target size.

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`

.. php:class:: Rotate

   .. php:method:: __construct($angle, Color $background = null)

      Constructs :php:class:`Imagine\\Filter\\Basic\\Rotate` instance

      :param integer             $angle:      Rotation angle.
      :param Imagine\\Image\\Color $background: Color to fill extra areas.

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`

.. php:class:: Save

   .. php:method:: __construct($path, array $options = array())

      Constructs :php:class:`Imagine\\Filter\\Basic\\Save` instance

      :param string $path:    Location to save the image to.
      :param array  $options: Options for save operation.

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`

.. php:class:: Show

   .. php:method:: __construct($format, array $options = array())

      Constructs :php:class:`Imagine\\Filter\\Basic\\Show` instance

      :param string $format:  Format to use to display the image.
      :param array  $options: Options for save operation.

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`

.. php:class:: Thumbnail

   .. php:method:: __construct(BoxInterface $size, $mode = ImageInterface::THUMBNAIL_INSET)

      Constructs :php:class:`Imagine\\Filter\\Basic\\Thumbnail` instance

      :param Imagine\\Image\\BoxInterface $size: Thumbnail size.
      :param string                     $mode: Thumbnail generation mode.

   .. php:method:: apply(ImageInterface $image)

      see :php:meth:`Imagine\\Filter\\FilterInterface::apply`
