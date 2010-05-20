<?php

namespace Imagine;

interface Image {
    public function getName();
    public function getType();
    public function getContentType();
    public function getContent();
    public function getHeight();
    public function getWidth();
    public function resize($width, $height);
    public function crop($x, $y, $width, $height);
}