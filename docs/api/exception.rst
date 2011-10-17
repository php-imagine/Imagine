namespace `Imagine\\Exception`
=============================

.. php:namespace:: Imagine\Exception

.. php:interface:: Exception

   Generic exception interface, allows catching all `Imagine` related exceptions.

.. php:class:: InvalidArgumentException

   This is exception is thrown whenever an invalid argument is provided.

   Extends :php:exc:`InvalidArgumentException`

   Implements :php:interface:`Imagine\\Exception\\Exception`

.. php:class:: OutOfBoundsException

   This is exception is thrown whenever value is not within expected boundaries.

   Extends :php:exc:`OutOfBoundsException`

   Implements :php:interface:`Imagine\\Exception\\Exception`

.. php:class:: RuntimeException

   This is exception is thrown whenever the underlying driver fails to perform.

   Extends :php:exc:`RuntimeException`

   Implements :php:interface:`Imagine\\Exception\\Exception`
