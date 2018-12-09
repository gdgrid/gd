<?php
/**
 * Class Adapter
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd\connect
{

    /**
     * show off @property, @property-read, @property-write
     * @property IConnector $connector;
     * */
    abstract class Adapter
    {
        const STORE_TIME = 60;

        const STORE_MAX_TIME = 3600;

        private $connector;

        private static $capture = [];

        protected $storeTime;

        protected static $storeMaxTime;

        final private function __construct(){ }

        /**
         * Retrieves a new instance of the current Adapter`s Connector Class.
         *
         * @return IConnector
         */
        abstract function fetchConnector(): IConnector;

        /**
         * Deserialize the current Adapter class from any available storage (File Cache as defaults).
         *
         * @return null|Adapter
         */
        abstract static function getStore();

        /**
         * Checks if storage time exceeds maximum allowed limit.
         *
         * returns "true" if outdated.
         * @return bool
         */
        abstract static function checkStoreOutdated(): bool;

        /**
         * Serializes the current Adapter class and puts it in any available storage (File Cache as defaults)
         * for a certain time, for further quick access to the already processed data.
         *
         * @param int $time
         *
         * @return mixed
         */
        abstract function setStore(int $time = 0);

        /**
         * @param IConnector|null $connector
         *
         * @return $this
         */
        final private function setConnector(?IConnector $connector)
        {
            $this->connector = $connector ?? $this->fetchConnector();

            $this->connector->attachAdapter($this)->init();

            return $this;
        }

        /**
         * @param int $time
         * @param IConnector|null $connector
         *
         * @return $this
         */
        final static function store(int $time = self::STORE_TIME, IConnector $connector = null)
        {
            if (self::checkStoreOutdated())

                return self::restore($time, $connector);

            if (($store = static::getStore()) && $store instanceof Adapter)

                return $store;

            return self::restore($time, $connector);
        }

        /**
         * @param int $time
         * @param IConnector|null $connector
         *
         * @return $this
         */
        final private static function restore(int $time = self::STORE_TIME, IConnector $connector = null)
        {
            /* @var $class $this */

            $call = get_called_class();

            $class = new $call;

            if ($connector or null === $class->connector)

                $class->setConnector($connector);

            $class->setStore($time);

            return $class;
        }

        /**
         * @return $this
         */
        final static function capture()
        {
            /* @var $class Adapter */

            $call = get_called_class();

            if (empty(self::$capture[$call]))

                self::$capture[$call] = new $call;

            return self::$capture[$call];
        }

        /**
         * @param string $m
         * @param array $arg
         *
         * @return mixed
         */
        final private function callConnector(string $m, array $arg = [])
        {
            if ($m === 'setConnector')

                return call_user_func_array([$this, $m], $arg);

            if (null === $this->connector)

                $this->setConnector(null);

            return call_user_func_array([$this->connector, $m], $arg);
        }

        /**
         * @param string $m
         * @param array $arg
         *
         * @return mixed
         */
        final function __call(string $m, array $arg = [])
        {
            return $this->callConnector($m, $arg);
        }

        /**
         * @param string $m
         * @param array $arg
         *
         * @return Adapter|mixed
         */
        final static function __callStatic(string $m, array $arg = [])
        {
            $call = get_called_class();

            return isset(self::$capture[$call]) ? self::$capture[$call]->callConnector($m, $arg) : (new $call)->callConnector($m, $arg);
        }
    }
}
