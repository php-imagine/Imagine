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
    $imageManager = new StandardImageManager();
    $image = $imageManager->fetch('/tmp/my_new_image.jpg');
    $image->resize(40, 50);
    $image->setName('resizedImage');
    $imageManager->save($image); // create resized.jpg

Cropping:

    <?php
    //...
    $image->crop(0, 0, 40, 50); // will crop image to 40 px width and 50 px height, starting at 0y and 0x position
    $image->setName('cropped');
    $imageManager->save($image); // create cropped.jpg

Happy coding!