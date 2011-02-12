#Imagine#

Image manipulation library for PHP 5.3 inspired by Python's PIL and other image
libraries.

##Requirements##

The Imagine library has the following requirements:

 - PHP 5.3+

Depending on the chosen Image implementation, you may need one of the following:

 - GD2
 - Imagick

##Basic usage##

###Open Existing Images###

To open an existing image, all you need is to instantiate an image factory and
invoke `Imagine::open()` with `$path` to image as the  argument

    <?php
    $imagine = new Imagine\Gd\Imagine();
    // or
    $imagine = new Imagine\Imagick\Imagine();
    
    $image = $imagine->open('/path/to/image.jpg');

The `Imagine::open()` method may throw one of the following exceptions:

 - Imagine\Exception\InvalidArgumentException
 - Imagine\Exception\RuntimeException

Now that you've opened an image, you can perform manipulations on it:

    <?php
    $image->resize(15, 25)
        ->rotate(45)
        ->crop(0, 0, 45, 45)
        ->save('/path/to/new/image.jpg');

###Create New Images###

Imagine also lets you create new, empty images. The following example creates an
empty image of width 400px and height 300px:

    <?php
    $image = $imagine->create(400, 300);

You can optionally specify the fill color for the new image, which defaults to
opaque white. The following example creates a new image with a fully-transparent
black background:

    <?php
    $image = $imagine->create(400, 300, new Imagine\Color('000', 100));

###Color Class###

Color is a class in Imagine, which takes two arguments in its constructor: the
RGB color code and a transparency percentage. The following examples are
equivalent ways of defining a fully-transparent white color.

    <?php
    $white = new Imagine\Color('fff', 100);
    $white = new Imagine\Color('ffffff', 100);
    $white = new Imagine\Color('#fff', 100);
    $white = new Imagine\Color('#ffffff', 100);

After you have instantiated a color, you can easily get its Red, Green, Blue and
Alpha (transparency) values:

    <?php
    var_dump(array(
        'R' => $white->getRed(),
        'G' => $white->getGreen(),
        'B' => $white->getBlue(),
        'A' => $white->getAlpha()
    ));

##Advanced Example - An Image Collage##

Assume we were given the not-so-easy task of creating a four-by-four collage of
16 student portraits for a school yearbook.  Each photo is 30x40px and we need
four rows and columns in our collage, so the final product will be 120x160px.

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

Here is how we would approach this problem with Imagine.

    <?php
    // make an empty image (canvas) 120x160px
    $collage = $imagine->create(120, 160);
    
    // starting coordinates (in pixels) for inserting the first image
    $x = 0;
    $y = 0;
    
    foreach (glob('/path/to/people/photos/*.jpg') as $path) {
        // open photo
        $photo = $imagine->open($path);
        
        // paste photo at current position
        $collage->paste($photo, $x, $y);
        
        // move position by 30px to the right
        $x += 30;
        
        if ($x >= 120) {
            // we reached the right border of our collage, so advance to the
            // next row and reset our column to the left.
            $y += 40;
            $x = 0;
        }
        
        if ($y >= 160) {
            break; // done
        }
    }
    
    $collage->save('/path/to/collage.jpg');

##Available Methods##

 - `->copy()` - duplicates current image and returns a new ImageInterface
     instance

 - `->crop($x, $y, $width, $height)` - crops the image, starting with the $x,
     $y coordinates and extending to the specified width and height

 - `->flipHorizontally()` - creates a horizontal mirror reflection of image

 - `->flipVertically()` - creates a vertical mirror reflection of image

 - `->paste(ImageInterface $image, $x, $y)` - pastes another image into the
     source image at the $x, $y coordinates

 - `->resize($width, $height)` - resizes image to given height and width
     exactly

 - `->rotate($angle, Color $background = null)` - rotates the image clockwise
     by the given angle, or counter-clockwise if the angle is negative. If a
     background color is given, it will be used to fill empty parts of the image
     (white will be used by default).
     
 - `->save($path, array $options = array())` - saves current image to the
     specified path. The target file extension will be used to infer the output
     format. For 'jpeg/jpg' and 'png' images, a 'quality' option of 0-100 and
     0-9 are accepted, respectively. 'png' images also accept a 'filter' option
     (consult the GD manual for more information). For 'wbmp' or 'xbm' images, a
     'foreground' option may be specified.
     
 - `->show($format, array $options = array())` - outputs image content in the
     given format, allowing the same options as the `save()` method
     
 - `->thumbnail($width, $height, $mode = self::THUMBNAIL_INSET)` - prepares an
     image thumbnail, based on the target dimensions, while preserving
     proportions. The thumbnail operation returns a new ImageInterface instance
     that is a processed copy of the original (the source image is not modified).
     If thumbnail mode is `ImageInterface::THUMBNAIL_INSET`, the original image
     is scaled down so it is fully contained within the thumbnail dimensions.
     The specified width and height will be considered maximum limits. Unless
     the given dimensions are equal to the original image's aspect ratio, one
     dimension in the resulting thumbnail will be smaller than the given limit.
     If `ImageInterface::THUMBNAIL_OUTBOUND` mode is chosen, then the thumbnail
     is scaled so that its smallest side equals the length of the corresponding
     side in the original image. Any excess outside of the scaled thumbnail's
     area will be cropped, and the returned thumbnail will have the exact width
     and height specified.

##Image Transformations##

Imagine also provides so-called image transformations.

Image transformation is implemented via the Transformation class, which mostly
conforms to ImageInterface and can be used interchangeably with it. The main
difference is that transformations may be stacked and performed on a real
ImageInterface instance later using the `apply()` method.

Example of a naive thumbnail implementation:

    <?php
    $transformation = new Imagine\Filter\Transformation();
    
    $transformation->thumbnail(30, 30)
        ->save('/path/to/resized/thumbnail.jpg');
    
    $transformation->apply($imagine->open('/path/to/image.jpg'));

The result of `apply()` is the modified image instance itself, so if we wanted
to create a mass-processing thumbnail script, we would do something like the
following:

    <?php
    $transformation = new Imagine\Filter\Transformation();
    
    $transformation->thumbnail(30, 30);
    
    foreach (glob(/path/to/lots/of/images/*.jpg) as $path) {
        $transformation->apply($imagine->open($path))
            ->save('/path/to/resized/'.md5($path).'.jpg');
    }

##Architecture##

The architecture is very flexible, as the filters don't need any processing
logic other than calculating the variables based on some settings and invoking
the corresponding method, or sequence of methods, on the ImageInterface
implementation.

The Transformation object is an example of a composite filter, representing a
stack or queue of filters, that get applied to an Image upon application of
the Transformation itself.

#TODO#

 - Update the ImagineBundle to use the new library
