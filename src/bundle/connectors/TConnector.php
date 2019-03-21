<?php

namespace gdgrid\gd\bundle\connectors
{

    use gdgrid\gd\bundle\Adapter;

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