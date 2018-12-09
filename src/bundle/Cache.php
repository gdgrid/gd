<?php
/**
 * Class Cache
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd\bundle
{

    /**
     * show off @property, @property-read, @property-write
     * */
    class Cache
    {
        const STORE_DIR = __DIR__ . '/../storage';

        static function get(string $key, $def = null)
        {
            if ($file = glob(self::STORE_DIR . '/' . self::hashName($key) . '*')[0])
            {
                if (($due = explode('_', $file)[1] ?? 0) && $due < time())
                {
                    unlink($file);

                    return null;
                }

                return @unserialize(file_get_contents($file)) ?: $def;
            }

            return $def;
        }

        static function set(string $key, $value, int $time = 0)
        {
            $hash = self::hashName($key) . ($time ? '_' . (time() + $time) : '');

            return file_put_contents(self::STORE_DIR . '/' . $hash, serialize($value));
        }

        static function delete(string $key)
        {
            if ($file = glob(self::STORE_DIR . '/' . self::hashName($key) . '*')[0])
            {
                unset($file);

                return true;
            }

            return false;
        }

        static function cachedAt(string $key): int
        {
            if ($file = glob(self::STORE_DIR . '/' . self::hashName($key) . '*')[0])

                return filemtime($file);

            return 0;
        }

        protected static function hashName(string $key)
        {
            return md5($key);
        }
    }
}
