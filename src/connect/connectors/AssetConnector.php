<?php
/**
 * Class AssetAdapter
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd\connect\connectors
{

    use gdgrid\gd\connect\Adapter;
    use gdgrid\gd\connect\IConnector;
    use gdgrid\gd\connect\Asset;
    
    /**
     * show off @property, @property-read, @property-write
     * @property $adapter Asset;
     * */
    class AssetConnector implements IConnector
    {
        private $adapter;

        protected $bundle;

        protected $assetDir;

        protected $sources;

        public function attachAdapter(Adapter $adapter): IConnector
        {
            $this->adapter = $adapter;

            return $this;
        }

        /**
         * @return Asset|Adapter
         */
        public function getAdapter(): Adapter
        {
            return $this->adapter;
        }

        public function setAssetDir(string $dir)
        {
            $this->assetDir = $dir;

            return $this;
        }

        public function getAssetDir()
        {
            return $this->assetDir ?? $this->getAdapter()->getAssetDir();
        }

        public function sources()
        {
            return $this->sources ?? $this->getAdapter()->fetchSources();
        }

        public function bundle()
        {

        }

        public function head()
        {

        }

        public function end()
        {

        }

        public function combine()
        {

        }

        public function get()
        {

        }
    }
}
