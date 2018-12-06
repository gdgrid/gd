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

        private static $capture;

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

        private function callConnector(Adapter $class, string $m, array $arg = [])
        {
            if ($m === 'setConnector')
            {
                $class->setConnector($arg[0]->attachAdapter($class));

                return $class->connector();
            }

            $class->setConnector($class->fetchConnector()->attachAdapter($class));

            return call_user_func_array([$class->connector(), $m], $arg);
        }



        public static function __callStatic(string $m, array $arg = [])
        {
            /* @var $class Adapter */

            $call = get_called_class();

            if ($m === 'capture' && false == isset(static::$capture[$call]))

                static::$capture[$call] = new $call;

            $class = new $call;

            if ($m === 'setConnector')
            {
                $class->setConnector($arg[0]->attachAdapter($class));

                return $class->connector();
            }

            $class->setConnector($class->fetchConnector()->attachAdapter($class));

            return call_user_func_array([$class->connector(), $m], $arg);
        }
    }
}
