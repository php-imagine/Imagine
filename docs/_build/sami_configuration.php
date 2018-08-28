<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in(dirname(dirname(__DIR__)) . '/src')
;

return new Sami($iterator, array(
    'title' => 'Imagine API',
    'theme' => 'default',
    'build_dir' => __DIR__ . '/../API/API',
    'cache_dir' => __DIR__ . '/cache',
    'default_opened_level' => 2,
));
