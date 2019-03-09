<?php

namespace gdgrid\gd\bundle\connectors
{

    use gdgrid\gd\bundle\Adapter;

    use gdgrid\gd\bundle\IConnector;

    /**
     * show off @property, @property-read, @property-write
     * */
    trait TConnector
    {
        private $adapter;

        public function init()
        {
        }

        /**
         * @param Adapter $adapter
         *
         * @return IConnector
         */
        public function attachAdapter(Adapter $adapter): IConnector
        {
            $this->adapter = $adapter;

            return $this;
        }

        /**
         * @return Adapter
         */
        public function adapter(): Adapter
        {
            return $this->adapter;
        }

        public function render()
        {
            ob_start();

            include(__DIR__ . '/render/' . trim(strtolower(preg_replace('/([A-Z])/', '-$1', __CLASS__)), '-') . '.php');

            return ob_get_clean();
        }
    }
}