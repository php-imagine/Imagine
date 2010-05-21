# Image Manipulation Library

Image manipulations APIs - I hate them. I also don't understand most of them.
Why can't I just do $image->resize($height, $width);
Well, now I can.

> Note. This library is in its very early alpha stage, please use carefully.

> Note. Feel free to fork it and contribute!

# Usage

Resizing:

    <?php
    // create filesystem image manager, more managers to come...
    $image = new Imagine\StandardImage('/tmp/my_new_image.jpg');
    $image->setName('resized'); // set new image name to be resized
    $imageProcessor = new Imagine\ImageProcessor();
    // create image processor, that knows to resize and save the image
    $imageProcessor->resize(40, 50);
    $imageProcessor->save('/tmp/');
    $imageProcessor->process($image); // create resized.jpg

Cropping:

    <?php
    //...
    // will crop image to 40 px width and 50 px height, starting at 0y and 0x
    $image->setName('cropped');
    $imageProcessor->crop(0, 0, 40, 50);
    $imageProcessor->save('/tmp/');
    $imageProcessor->process($image); // create cropped.jpg

Combination of processes:

    <?php
    //...
    // will resize image to 50x50, constraining proportions and cropping the bottom
    // will replace the existing image with the new one
    $imageProcessor->resize(50, true)
        ->crop(0, 0, 50, 50)
        ->save('/tmp/')
        ->process($image);

You can also undo image modifications:

    <?php
    //...
    $imageProcessor->restore($image);
    // note, that you have to re-save restored image if you had save() command
    // on stack when you were initially processing the image
    $imageProcessor->save('/tmp/')
        ->process($image);

> Note: You can undo any number of modifications stacked on image processor instance.
> After ImageProcessor::restore() method is called, all commands are cleared from
> ImageProcessor instance. You will have to re-stack ImageProcessor to continue
> image processing

Bulk processing (awesome):

    <?php
    //...
    // create create processor, that resizes images, to 50 px width, maintains
    // image proportions, crops the extra height, from the bottom, and re-saves
    // the updated image
    $imageProcessor->resize(50, true)
        ->crop(0, 0, 50, 50)
        ->save('/tmp/');

    foreach (glob(*.jpg) as $path) {
        $image = new Imagine\StandardImage($path);
        $image->setName($image->getName() . '_processed'); // rename updated image
        $imageProcessor->process($image); // processes and saves appending '_processed' to file name
        unset ($image); // clean up memory.
    }

Happy coding!