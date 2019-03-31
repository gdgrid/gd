<?php
/**
 * Class AssetConnector
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

namespace gdgrid\gd\bundle\connectors
{

    use Exception;

    /**
     * show off @property, @property-read, @property-write
     * */
    class AssetConnector implements IConnector
    {
        use TConnector;

        protected $assets = [];

        protected $sourceDir = [];

        protected $outputDir;

        protected $compile = false;

        protected $compileIp = ['127.0.0.1', '::1'];

        public function __construct()
        {
            $this->compile = in_array(getenv('REMOTE_ADDR'), $this->compileIp);
        }

        /**
         * @param array $dir
         * @return $this
         */
        public function setSourceDir(array $dir)
        {
            $this->sourceDir = $dir;

            return $this;
        }

        /**
         * @param string $dir
         * @return $this
         */
        public function setOutputDir(string $dir)
        {
            $this->outputDir = $dir;

            return $this;
        }

        public function setCompileIp(array $ip)
        {
            $this->compileIp = $ip;

            $this->compile = in_array(getenv('REMOTE_ADDR'), $this->compileIp);

            return $this;
        }

        public function find()
        {
            if ($this->compile !== true)

                return $this;


        }

        public function filter(string $glob, callable $filter = null)
        {

        }

        public function output()
        {
            return $this->assets;
        }
    }
}
