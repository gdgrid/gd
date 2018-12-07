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
     * @method static capture();
     * */
    abstract class Adapter
    {
        private $connector;

        private static $capture = [];

        abstract function fetchConnector(): IConnector;

        final private function setConnector(IConnector $connector)
        {
            $this->connector = $connector;

            $this->connector->attachAdapter($this)->init();

            return $this;
        }

        final private function callConnector(string $m, array $arg = [])
        {
            if ($m === 'setConnector')

                return call_user_func([$this, $m], $arg[0]);

            if (null === $this->connector)

                $this->setConnector($this->fetchConnector());

            return call_user_func_array([$this->connector, $m], $arg);
        }

        final function __call(string $m, array $arg = [])
        {
            return $this->callConnector($m, $arg);
        }

        final static function __callStatic(string $m, array $arg = [])
        {
            /* @var $class Adapter */

            $call = get_called_class();

            if ($m === 'capture')
            {
                if (empty(self::$capture[$call]))

                    self::$capture[$call] = new $call;

                return self::$capture[$call];
            }

            return isset(self::$capture[$call])

                ? self::$capture[$call]->callConnector($m, $arg) : (new $call)->callConnector($m, $arg);
        }
    }
}
