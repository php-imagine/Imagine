<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$title = 'Imagine API';
$srcDir = dirname(dirname(__DIR__)) . '/src';

$imagineInterfaceSource = file_get_contents("{$srcDir}/Image/ImagineInterface.php");
if (preg_match('/^\s*const\s+VERSION\s*=\s*(["\'])(.*)\\1\s*;/m', $imagineInterfaceSource, $m)) {
    $title .= ' v' . ltrim($m[2], 'v. ');
}

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in($srcDir)
;

return new Sami($iterator, array(
    'title' => $title,
    'theme' => 'default',
    'build_dir' => __DIR__ . '/../API/API',
    'cache_dir' => __DIR__ . '/cache',
    'default_opened_level' => 2,
));
