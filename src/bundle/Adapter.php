<?php
/**
 * Class Adapter
 *
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

    use gdgrid\gd\bundle\connectors\IConnector;

    /**
     * show off @property, @property-read, @property-write
     *
     * @property IConnector $connector;
     * */
    abstract class Adapter
    {
        const STORE_TIME = 60;

        const STORE_MAX_TIME = 3600;

        /**
         * The current Adapter`s Connector instance, retrieved by the "fetchConnector()" method.
         */
        private $connector;

        /**
         * The static method "capture()" of the current Adapter, creates a single instance of the current object
         * and stores it in the "$capture" property.
         */
        protected static $capture = [];

        /**
         * The current Adapter`s instance time to store in seconds.
         */
        protected $storeTime;

        /**
         * The maximum allowable storage time for the current instance of the Adapter, after which it is recreated.
         */
        protected $storeMaxTime;

        /**
         * Adapter constructor blocked, make all initializations in the Connector`s "init" method instead.
         */
        final private function __construct()
        {
        }

        /**
         * Retrieves a new instance of the current Adapter`s Connector Class.
         *
         * @return IConnector
         */
        abstract function fetchConnector(): IConnector;

        /**
         * Deserialize the current Adapter`s instance from any available storage (File Cache as defaults).
         *
         * @return null|Adapter
         */
        abstract function getStore();

        /**
         * Checks if storage time exceeds maximum allowed limit.
         *
         * returns "true" if outdated.
         *
         * @return bool
         */
        abstract function checkStoreOutdated(): bool;

        /**
         * Serializes the current Adapter`s instance and puts it in any available storage (File Cache as defaults)
         * for a certain time, for further quick access to the already processed data.
         *
         * @param int $time
         *
         * @return mixed
         */
        abstract function setStore(int $time = null);

        /**
         * @param IConnector|null $connector
         *
         * @return $this
         */
        final private function setConnector(IConnector $connector = null)
        {
            $this->connector = $connector ?? $this->fetchConnector();

            $this->connector->attachAdapter($this)->init();

            return $this;
        }

        /**
         * @param int             $time
         * @param IConnector|null $connector
         *
         * @return $this
         */
        final private function store(int $time = self::STORE_TIME, IConnector $connector = null)
        {
            if (false == $this->checkStoreOutdated() && ($store = $this->getStore()) && $store instanceof Adapter)

                return $store;

            return $this->restore($time, $connector);
        }

        /**
         * @param int             $time
         * @param IConnector|null $connector
         *
         * @return $this
         */
        final private function restore(int $time = self::STORE_TIME, IConnector $connector = null)
        {
            if ($connector or null === $this->connector)

                $this->setConnector($connector);

            $this->setStore($time);

            return $this;
        }

        /**
         * @return $this
         */
        final static function capture()
        {
            /* @var $class Adapter */

            $call = get_called_class();

            if (empty(static::$capture[$call]))

                static::$capture[$call] = new $call;

            return static::$capture[$call];
        }

        /**
         * @param string $m
         * @param array  $arg
         *
         * @return mixed
         */
        final private function callConnector(string $m, array $arg = [])
        {
            if ($m === 'setConnector' || $m === 'store' || $m === 'restore')

                return call_user_func_array([$this, $m], $arg);

            if (null === $this->connector)

                $this->setConnector(null);

            return call_user_func_array([$this->connector, $m], $arg);
        }

        /**
         * @param string $m
         * @param array  $arg
         *
         * @return mixed
         */
        final function __call(string $m, array $arg = [])
        {
            return $this->callConnector($m, $arg);
        }

        /**
         * @param string $m
         * @param array  $arg
         *
         * @return Adapter|mixed
         */
        final static function __callStatic(string $m, array $arg = [])
        {
            $call = get_called_class();

            $instance = static::$capture[$call] ?? new $call;

            return $instance->callConnector($m, $arg);
        }
    }
}