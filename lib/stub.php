<?php
Phar::mapPhar();

$basePath = 'phar://' . __FILE__ . '/';

spl_autoload_register(function($class) use ($basePath)
{
    if (0 !== strpos($class, "Imagine\\")) {
        return false;
    }
    $path = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 8));
    $file = $basePath.$path.'.php';
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
});

__HALT_COMPILER();
