<?php

spl_autoload_register(function($className)
{
    $path = explode('\\', str_replace('gdgrid\gd\\', '', $className));

    $cn = array_pop($path);

    $path = array_slice($path, 1);

    $file = dirname(dirname(__FILE__))
        . DIRECTORY_SEPARATOR
        . ($path ? join(DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR : '')
        . $cn
        . '.php';

    if (false === is_file($file))

        throw new \Exception('Class "' . $className . '" not found.');

    require_once($file);
});
