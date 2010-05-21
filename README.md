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
    $imageManager = new Imagine\StandardImageManager();
    $image = $imageManager->fetch('/tmp/my_new_image.jpg');
    $imageProcessor = new Imagine\ImageProcessor();
    $imageProcessor->resize(40, 50);
    $imageProcessor->process($image);
    $image->setName('resized');
    $imageManager->save($image); // create resized.jpg

Cropping:

    <?php
    //...
    $imageProcessor->crop(0, 0, 40, 50); // will crop image to 40 px width and 50 px height, starting at 0y and 0x position
    $imageProcessor->process($image);
    $image->setName('cropped');
    $imageManager->save($image); // create cropped.jpg

Combination of processes:

    <?php
    //...
    $imageProcessor->resize(50, true)
        ->crop(0, 0, 50, 50)
        ->process($image); // will resize image to 50x50, constraining proportions and cropping the bottom

Bulk processing:

    <?php
    //...
    $imageProcessor->resize(50, true)
        ->crop(0, 0, 50, 50);

    foreach (glob(*.jpg) as $path) {
        $image = new Imagine\StandardImage($path);
        $imageProcessor->process($image);
        $image->setName($image->getName() . '_processed');
        $imageManager->save($image);
        unset ($image);
    }

Happy coding!