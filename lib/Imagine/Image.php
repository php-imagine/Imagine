<?php

namespace Imagine;

interface Image {
    public function getName();
    public function setName($name);
    public function getType();
    public function setType($type);
    public function getContentType();
    public function setContentType($contentType);
    public function getContent();
    public function setContent($content);
    public function getHeight();
    public function setHeight($height);
    public function getWidth();
    public function setWidth($width);
    public function getResource();
    public function setPath($path);
    public function getPath();
    public function getExtension();
}