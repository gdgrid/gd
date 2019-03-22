<?php

namespace gdgrid\gd\bundle
{

    use gdgrid\gd\bundle\connectors\IConnector;

    /**
     * show off @property, @property-read, @property-write
     * */
    trait TAdapter
    {
        public function fetchConnector(): IConnector
        {
            $conn = '\\gdgrid\\gd\\bundle\\connectors\\' . substr(strrchr(__CLASS__, "\\"), 1) . 'Connector';

            return new $conn;
        }

        public function setStore(int $time = null)
        {
            return Cache::set(__CLASS__, $this, intval($time ?? $this->storeTime));
        }

        public function getStore()
        {
            return Cache::get(__CLASS__);
        }

        public function checkStoreOutdated(): bool
        {
            $limit = intval($this->storeMaxTime ?? static::STORE_MAX_TIME);

            $time = Cache::cachedAt(__CLASS__);

            return $time === 0 || (time()-$time) > $limit;
        }
    }

    /**
     * show off @property, @property-read, @property-write
     * */
    class Cache
    {
        const STORE_DIR = __DIR__ . '/../storage';

        static function get(string $key, $def = null)
        {
            if ($file = self::getFile($key))
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
            $hash = self::hashName($key) . ($time ? '_' . (time()+$time) : '');

            return file_put_contents(self::STORE_DIR . '/' . $hash, serialize($value));
        }

        static function delete(string $key)
        {
            if ($file = self::getFile($key))
            {
                unset($file);

                return true;
            }

            return false;
        }

        static function cachedAt(string $key): int
        {
            if ($file = self::getFile($key))

                return filemtime($file);

            return 0;
        }

        protected static function getFile(string $name)
        {
            if ($files = glob(self::STORE_DIR . '/' . self::hashName($name) . '*'))
            {
                $file = sizeof($files) > 1 ? array_pop($files) : $files[0];

                if (sizeof($files) > 1)
                {
                    for ($i = 0; $i < sizeof($files); ++$i)
                    {
                        unlink($files[$i]);
                    }
                }

                return $file;
            }

            return null;
        }

        protected static function hashName(string $key)
        {
            return md5($key);
        }
    }
}