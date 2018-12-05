<?php
/**
 * @author GD Lab <dev.gdgrid@gmail.com>
 */

define('APP_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

DB::capsule();
