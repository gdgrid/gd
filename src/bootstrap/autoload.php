<?php
/***
 *  Package autoloader. If you do not use composer autoloader in your project,
 *  then include this file to your application`s initialization file.
 **/
spl_autoload_register(function($className)
{
    if (strpos($className, 'gdgrid\gd') !== false)
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
    }
});
