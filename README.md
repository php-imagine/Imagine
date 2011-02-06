#Imagine#

Image manupulation library for PHP 5.3 inspired by Python's PIL and other image
librarires

#Requirements#

The Imagine library has the following requirements:

 - PHP 5.3+

Depending on chosen Image implementation, you might need on of the following:

 - GD2
 - Imagick

#Basic usage#

##Open Existing Image##

To open an existing image, all you need is to instantiate a correct image
factory and invoke `Imagine::open()` with `$path` to image as the  argument

    <?php
    $imagine = new Imagine\Gd\Imagine();
    // or
    $imagine = new Imagine\Imagick\Imagine();
    
    $image = $imagine->open('/path/to/image.jpg');

The `Imagine::open()` might throw one of the following exceptions:
 - Imagine\Exception\InvalidArgumentException
 - Imagine\Exception\RuntimeException

Now that you opened an image, you can perform manupulations on it:

    <?php
    $image->resize(15, 25)
        ->rotate(45)
        ->crop(0, 0, 45, 45)
        ->save('/path/to/new/image.jpg');

##Create new image##

Imagine also let's you create a new empty image:

    <?php
    $image = $imagine->create(400, 300);

The above example would create an empty image of width 400px and height 300px

You can optionally specify the colorfill of the newly created image (defaults
to white):

    <?php
    $image = $imagine->create(400, 300, new Imagine\Color('000', 100));

The above example creates a new empty image with fully transparet black
background

##Color class##

`Color` is a class in Imagine, it takes two arguments in constructor - the
color and transparency percent

Here is how you would create a fully transparent white color:

    <?php
    $white = new Imagine\Color('fff', 100);
    $white = new Imagine\Color('ffffff', 100);
    $white = new Imagine\Color('#fff', 100);
    $white = new Imagine\Color('#ffffff', 100);

After you have instantiated a color, you can easily get its Red, Green, Blue
and Alpha (transparency) values:

    <?php
    var_dump(array(
        'R' => $white->getRed(),
        'G' => $white->getGreen(),
        'B' => $white->getBlue(),
        'A' => $white->getAlpha()
    ));

#Advanced example - images collage#

Assume we were tasked with a not so easy task - create a four by four collage
of 16 people photos for school (each photo is 30x40 px). We need a four rows
and four column collage, that will be of 120x160 px in dimensions.

The collage would look something like the following:

    -----------------
    |   |   |   |   |
    |   |   |   |   |
    -----------------
    |   |   |   |   |
    |   |   |   |   |
    -----------------
    |   |   |   |   |
    |   |   |   |   |
    -----------------
    |   |   |   |   |
    |   |   |   |   |
    -----------------

Here is how we would approach the problem with Imagine.

    <?php
    $collage = $imagine->open(120, 160);
    
    $x = 0;
    $y = 0;

    foreach (glob('/path/to/people/photos/*.jpg') as $path) {
        $photo = $imagine->open($path);
        
        $collage->paste($photo, $x, $y); // paste photo at current position
        
        $x += 30; // move position by 30px to the right
        
        if ($x >= 120) {
            // we reached the right border of our collage
            $y += 40; // go to the next row
            $x = 0; // start at the begining
        }
        
        if ($y >= 160) {
            break; // done
        }
    }
    
    $collage->save('/path/to/collage.jpg');

#Available methods#

 - `->copy()` - duplicates current image and returns new ImageInterface
     instance

 - `->crop($x, $y, $width, $height)` - crops a part of image starting with $x,
     $y coordinates and creating a rectangle of sepecified width and height

 - `->flipHorizontally()` - creates a horizontal mirror reflection of image

 - `->flipVertically()` - creates a vertical mirror reflection of image

 - `->paste(ImageInterface $image, $x, $y)` - pastes another image onto source
     image at the $x, $y coordinates

 - `->resize($width, $height)` - resizes image to given height and width
     exactly

 - `->rotate($angle, Color $background = null)` - rotates image for a specified
     angle CW, if the angle is negative - rotates CCW, background color fill
     can be specified to determine how to fill empty part of the image, white
     will be used by default
     
 - `->save($path, array $options = array())` - saves current image at the
     specified path, the target file extension will be used to determine save
     format. For 'jpeg/jpg', 'png' images, 'quality' options of 0-100 and 0-9 is
     available accordingly. 'png' images also accept 'filter' option, consult GD
     manual for a list of available options. Images of type 'wbmp' or 'xbm',
     'foreground' option might be specified
     
 - `->show($format, array $options = array())` - outputs image content. Options
     are the same as in save() method
     
 - `->thumbnail($width, $height, $mode = self::THUMBNAIL_INSET)` - prepares
     image thumbnail, based on the target dimensions, constraining proportions.
     Thumbnail operation doesn't modify the source image and returns a
     processed copy of the original. If thumbnail mode is
     `ImageInterface::THUMBNAIL_INSET`, the image is scaled down to cointain
     the full original image. This mode does not necessarily produce thumbnails
     of exact target size, it is rather ensuring that the whole image is
     resized adaptively to not exceed the specified thumbnail box.
     If `ImageInterface::THUMBNAIL_OUTBOUND` mode is chosen, then the thumbnail
     is resized to the so that its smallest side equals to the appropriate
     side's target length and the excess picture is cropped out.

#Image Transformations#

Imagine also provides so-called image transformations.

Image transformation is a class, that fully conforms to ImageInterface and can
be used interchangeably with it, the main difference is that transformations
are stacked and performed on a real Image instance later, using ->apply() method

Example, naive thumbnail implementation:

    <?php
    $transformation = new Imagine\Filter\Transformation();
    
    $transformation->thumbnail(30, 30)
        ->save('/path/to/resized/thumbnail.jpg');
    
    $transformation->apply($imagine->open('/path/to/image.jpg'));

The result of transformation apply is the modified image instance itself, so if
we wanted to create a mass processing thumbnail script, we would do something
like the following:

    <?php
    $transformation = new Imagine\Filter\Transformation();
    
    $transformation->thumbnail(30, 30);
    
    foreach (glob(/path/to/lots/of/images/*.jpg) as $path) {
        $transformation->apply($imagine->open($path))
            ->save('/path/to/resized/'.md5($path).'.jpg');
    }

#Architechture#

The architechture is very flexible, as the filters don't need any processing
logic other than calculating the variables based on some settings and invoke
corresponding method or a sequence of methods on the ImageInterface
implementation.

The Tranformation object is an example of a composite filter, that represents
a stack or queue of filters, that get applied to an Image upon application of
the Tranformation itself.

#TODO#

 - update the ImagineBundle to use the new library
