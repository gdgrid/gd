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
        private $connector;

        private static $capture = [];

        abstract function fetchConnector(): IConnector;

        private function setConnector(IConnector $connector)
        {
            $this->connector = $connector;

            return $this;
        }

        public function connector()
        {
            return $this->connector;
        }

        private function callConnector(string $m, array $arg = [])
        {
            if ($m === 'setConnector')
            {
                $this->setConnector($arg[0]->attachAdapter($this));

                return $this->connector();
            }

            if (null === $this->connector())

                $this->setConnector($this->fetchConnector()->attachAdapter($this)->init());

            return call_user_func_array([$this->connector(), $m], $arg);
        }

        public function __call(string $m, array $arg = [])
        {
            return $this->callConnector($m, $arg);
        }

        public static function __callStatic(string $m, array $arg = [])
        {
            /* @var $class Adapter */

            $call = get_called_class();

            if ($m === 'capture')
            {
                if (empty(static::$capture[$call]))

                    static::$capture[$call] = new $call;

                return static::$capture[$call];
            }

            return isset(static::$capture[$call])

                ? static::$capture[$call]->callConnector($m, $arg) : (new $call)->callConnector($m, $arg);
        }
    }
}
