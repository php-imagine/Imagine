Imagine
=======

Image manupulation library for PHP 5.3 inspired by Python's PIL and other image
librarires

Requirements
============

The Imagine library has the following requirements:

 - PHP 5.3+

Depending on chosen Image implementation, you might need on of the following:

 - GD2
 - Imagick

Basic usage
===========

Open Existing Image
----------
To open an existing image, all you need is to instantiate a correct image
implementation with the path to image on local/remote FS as the only argument

    <?php
    $image = new Imagine\Gd\FileImage('/path/to/image.jpg');

The FileImage constructor might throw one of the following exceptions:
 - Imagine\Exception\InvalidArgumentException
 - Imagine\Exception\RuntimeException

For the Dependency Injection fans out there, there is an alternative syntax:

    <?php
    $factory = new Imagine\Gd\ImageFactory();
    
    $image = $factory->open('/path/to/image.jpg');

Now that you opened an image, you can perform manupulations on it:

    <?php
    $image->resize(15, 25)
        ->rotate(45)
        ->crop(0, 0, 45, 45)
        ->save('/path/to/new/image.jpg');

Available methods
=================

 - ->copy() - duplicates current image and returns new ImageInterface instance

 - ->crop($x, $y, $width, $height) - crops a part of image starting with $x, $y
     coordinates and creating a rectangle of sepecified width and height

 - ->flipHorizontally() - creates a horizontal mirror reflection of image

 - ->flipVertically() - creates a vertical mirror reflection of image

 - ->paste(ImageInterface $image, $x, $y) - pastes another image onto source
     image at the $x, $y coordinates

 - ->resize($width, $height) - resizes image to given height and width exactly

 - ->rotate($angle, Color $background = null) - rotates image for a specified
     angle CW, if the angle is negative - rotates CCW, background color fill
     can be specified to determine how to fill empty part of the image, white
     will be used by default
     
 - ->save($path, array $options = array()) - saves current image at the
     specified path, the target file extension will be used to determine save
     format. For 'jpeg/jpg', 'png' images, 'quality' options of 0-100 and 0-9 is
     available accordingly. 'png' images also accept 'filter' option, consult GD
     manual for a list of available options. Images of type 'wbmp' or 'xbm',
     'foreground' option might be specified
     
 - ->show($format, array $options = array()) - outputs image content. Options
     are the same as in save() method

Image Transformations
=====================

Imagine also provides so-called image transformations.

Image transformation is a class, that fully conforms to ImageInterface and can
be used interchangeably with it, the main difference is that transformations
are stacked and performed on a real Image instance later, using ->apply() method

Example, naive thumbnail implementation:

    <?php
    $transformation = new Imagine\Filter\Transformation();
    
    $transformation->resize(40, 30)
        ->crop(5, 0, 30, 30)
        ->save('/path/to/resized/thumbnail.jpg');
    
    $transformation->apply(new Imagine\Gd\FileImage('/path/to/image.jpg'));

The result of transformation apply is the modified image instance itself, so if
we wanted to create a mass processing thumbnail script, we would do something
like the following:

    <?php
    $transformation = new Imagine\Filter\Transformation();
    
    $transformation->resize(40, 30)
        ->crop(5, 0, 30, 30);
    
    foreach (glob(/path/to/lots/of/images/*.jpg) as $path) {
        $transformation->apply(new Imagine\Gd\FileImage($path))
            ->save('/path/to/resized/'.md5($path).'.jpg');
    }
