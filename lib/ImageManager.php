<?php

namespace Imagine;

interface ImageManager {
    public function save(Image $image);
    public function delete(Image $image);
    public function fetchImage($args);
}