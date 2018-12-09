<?php

namespace gdgrid\gd\connect\connectors
{

    use gdgrid\gd\connect\Adapter;

    use gdgrid\gd\connect\IConnector;

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
    }
}