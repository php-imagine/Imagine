.. php:namespace:: Imagine\Filter

.. php:class:: Transformation

   .. php:method:: Transformation::__construct()

      Class constructor.

      :param ImagineInterface $imagine: An ImagineInterface instance

   .. php:method:: Transformation::applyFilter()

      Applies a given FilterInterface onto given ImageInterface and returns
      modified ImageInterface

      :param Imagine\\Filter\\FilterInterface $filter:
      :param Imagine\\Image\\ImageInterface $image:

      :returns: Imagine\\Image\\ImageInterface

      :throws: Imagine\\Exception\\InvalidArgumentException

   .. php:method:: Transformation::apply()

      (non-PHPdoc)

      :see: Imagine\\Filter\\FilterInterface::apply()

   .. php:method:: Transformation::copy()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::copy()

   .. php:method:: Transformation::crop()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::crop()

   .. php:method:: Transformation::flipHorizontally()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::flipHorizontally()

   .. php:method:: Transformation::flipVertically()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::flipVertically()

   .. php:method:: Transformation::strip()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::strip()

   .. php:method:: Transformation::paste()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::paste()

   .. php:method:: Transformation::applyMask()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::applyMask()

   .. php:method:: Transformation::fill()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::fill()

   .. php:method:: Transformation::resize()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::resize()

   .. php:method:: Transformation::rotate()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::rotate()

   .. php:method:: Transformation::save()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::save()

   .. php:method:: Transformation::show()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::show()

   .. php:method:: Transformation::thumbnail()

      (non-PHPdoc)

      :see: Imagine\\Image\\ManipulatorInterface::thumbnail()

   .. php:method:: Transformation::add()

      Registers a given FilterInterface in an internal array of filters for
      later application to an instance of ImageInterface

      :param Imagine\\Filter\\FilterInterface $filter:

      :returns: Imagine\\Filter\\Transformation