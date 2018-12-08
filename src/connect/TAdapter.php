<?php

namespace gdgrid\gd\connect
{
    use gdgrid\gd\bundle\Cache;

    /**
     * show off @property, @property-read, @property-write
     * */
    trait TAdapter
    {
        public function fetchConnector(): IConnector
        {
            $conn =  __CLASS__ . 'Connector';

            require_once 'connectors' . DIRECTORY_SEPARATOR . $conn . '.php';

            return new $conn;
        }

        static function getStore()
        {
            return Cache::get(__CLASS__);
        }

        static function checkStoreOutdated(): bool
        {
            $limit = intval(self::$storeMaxTime ?? self::STORE_MAX_TIME);

            $time = Cache::cachedAt(__CLASS__);

            return $time === 0 || (time() - $time) > $limit;
        }

        public function setStore(int $time = 0)
        {
            return Cache::set(__CLASS__, $this, intval($this->storeTime ?? $time));
        }
    }
}