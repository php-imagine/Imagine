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

##Create new image##

Imagine also let's you create a new empty image:

    <?php
    $image = new Imagine\Gd\BlankImage(400, 300);
    
    // or create a new image with fully transparent white background
    
    $image = new Imagine\Gd\BlankImage(400, 300, new Imagine\Color('fff', 100));

Again, for Dependency Injection fans:

    <?php
    $factory = new Imagine\Gd\ImageFactory();
    
    $image = $factory->create(400, 300);

Both above examples would create an empty image of width 400px and height 300px

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
    $collage = new Imagine\Gd\BlankImage(120, 160);
    
    $x = 0;
    $y = 0;

    foreach (glob('/path/to/people/photos/*.jpg') as $path) {
        $photo = new Imagine\Gd\FileImage($path);
        
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

#Image Transformations#

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

#Complex Filters#

Although `Transformation` lets you pre-define any complex tranformations, it
is sometimes a tedious task to perform, therefore Imagine comes with some of
the most common transformations pre-built, they're called advanced filters
and can be found under `Imagine\Filter\Advanced` namespace.

##Thumbnail##

Thumbnail generation is probably the most wide-spread image processing tesk a
PHP developer faces.

Using Imagine its no problem anymore.

###Simple Thumbnail Generation###

    <?php
    // make thumbnail filter for generating 50x50 px thumbs
    $filter = new Imagine\Filter\Advanced\Thumbnail(50, 50);
    
    $image = new Imagine\Gd\FileImage('/path/to/image.jpg');
    
    $filter->apply($image)
        ->save('/path/to/save/thumbnail.jpg');

The default thumbnail generation technique can be described in the following
steps:

 1. Check if one of the sides of the target image is less than target tumbnail
    size, if false, proceed to step `4`.
 2. Create a new empty image with white background. Make image of sides of
    at least target thumbnail side length.
 3. Place the source image at the center of the newly created image.
 4. Resize image sides, constraining proportions so the smallest side is of
    the length or according side of the target thumbnail dimensions.
 5. Crop the middle of the image out to get rid of excess size.
 6. Return the cropped image.

NOTE: to change the background color fill, you have to instantiate `Thumbnail`
filter with UPSCALE_COLOR strategy and fourth argument being the actual color

    <?php
    // make thumbnail background black and 50% transparent
    $filter = new Imagine\Filter\Advanced\Thumbnail(50, 50, Imagine\Filter\Advanced\Thumbnail::UPSCALE_COLOR, new Imagine\Color('000', 50));

###Thumbnail upscaling###

Another strategy for thumbnail generation is by first upscaling the image to
meet the minimum thumbnail measurement requirements and then crop the excess
size from the middle.

    <?php
    $filter = new Imagine\Filter\Advanced\Thumbnail(50, 50, Imagine\Filter\Advanced\Thumbnail::UPSCALE_RESIZE)

###Thumbnail stretching###

The last generation strategy is to strech side that is smaller than its target
couterpart to fit minimum length constraint.

    <?php
    $filter = new Imagine\Filter\Advanced\Thumbnail(50, 50, Imagine\Filter\Advanced\Thumbnail::UPSCALE_STRETCH)

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
 - implement Imagick library support
