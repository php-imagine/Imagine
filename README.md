#Imagine#

Image manipulation library for PHP 5.3 inspired by Python's PIL and other image
libraries.

##Requirements##

The Imagine library has the following requirements:

 - PHP 5.3+

Depending on the chosen Image implementation, you may need one of the following:

 - GD2
 - Imagick
 - Gmagick

##Basic Principles##

The main premise of Imagine is to provide all the necessary low-level methods to bring all native PHP image processing libraries to the same intuitive OO API.

For that, several things are necessary:

* Image manipulation tools, such as resize, crop, etc.
* Drawing api - to create basic shapes and advanced charts, write text on the image
* Masking functionality - ability to apply black&white and grayscale images as masks, leadin to semi-transparency (in case of grayscale) of the parent image or absolute transparency regions (in cas of black&white)

The above tools should be the basic foundation for a more powerful set of tools or how we call them in Imagine - ``Filters``.

Some of the ideas for upcoming filters:

* Charting and graphing filters (pie and bar charts, linear graphs with annotations)
* Reflection - as simple as flipping an image vertically and applying grayscale gradient image as a mask, and pasting both onto a canvas of twice the height of the original image
* Rounded corners - can be easily done by using drawing api to create a black rectangle with elliptical corners, and applying it as a mask to the desired image
* ideas welcome...

## Documentation ##

 - [Introduction](/avalanche123/Imagine/blob/master/docs/en/introduction.rst "Introduction")
 - [Coordinate System](/avalanche123/Imagine/blob/master/docs/en/coordinates.rst "Coordinate System")
 - [Filters](/avalanche123/Imagine/blob/master/docs/en/filters.rst "Filters and Transformations")
 - [Image](/avalanche123/Imagine/blob/master/docs/en/image.rst "Image")
 - [Drawing](/avalanche123/Imagine/blob/master/docs/en/drawing.rst "Drawing")
 - [Exceptions](/avalanche123/Imagine/blob/master/docs/en/exceptions.rst "Exceptions")