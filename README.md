# Image Manipulation Library

Image manipulations APIs - I hate them. I also don't understand most of them.
Why can't I just do `$image->resize($height, $width);`?  Well, now I can do
better.

> Note. This library is in its very early alpha stage, please use carefully.

> Note. Feel free to fork it and contribute!

## Usage

Resizing:

    <?php
    // create image
    $image = new Imagine\Image('/tmp/my_image.jpg');
    // create image processor, that knows to resize and save the image
    $processor = new Imagine\Processor();
    $processor
        ->resize(100, 100)
        ->save()
        ->process($image); // overwrite my_image.jpg with resized version

Cropping:

    <?php
    //...
    // will crop image to 40 px width and 50 px height, starting at 0x and 0y
    $processor
        ->crop(0, 0, 40, 50)
        ->save('/tmp/cropped.jpg') // destination path
        ->process($image); // save the cropped version to cropped.jpg

Combination of processes:

    <?php
    //...
    // will resize image to 50x50, constraining proportions and cropping the bottom
    // will replace the existing image with the new one
    $processor
        ->resize(50, true)
        ->crop(0, 0, 50, 50)
        ->save()
        ->process($image);

Bulk processing (awesome):

    <?php
    //...
    // create create processor, that resizes images, to 50 px width, maintains
    // image proportions, crops the extra height, from the bottom, and re-saves
    // the updated image
    $processor
        ->resize(50, true)
        ->crop(0, 0, 50, 50)
        ->save();

    foreach (glob(*.jpg) as $path) {
        $image = new Imagine\Image($path);
        $processor->process($image); // processes and overwrites the file
    }

Happy coding!