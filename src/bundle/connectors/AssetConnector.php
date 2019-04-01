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

        public function isCompile()
        {
            return $this->compile === true;
        }

        public function find(callable $getOutputSubDir = null)
        {
            if ($this->isCompile() === false)

                return $this;

            for ($i = 0; $i < sizeof($this->sourceDir); ++$i)
            {
                $this->scandir($this->sourceDir[$i], function($file, $dir, $depth) use (& $outputSubDir, $getOutputSubDir)
                {
                    if ($depth == 1)
                    {
                        $outputSubDir = $getOutputSubDir

                            ? call_user_func($getOutputSubDir, $dir) : (new \SplFileInfo($dir))->getMTime();

                        $outputDir = $this->outputDir . '/' . trim($outputSubDir, '/');
                    }
                    else
                    {
                        $outputDir = $this->outputDir . '/' . trim($outputSubDir, '/') . '/' . ;
                    }

                });
            }

            return $this;
        }

        public function scandir(string $dir, callable $handle, int $depth = 0)
        {
            if ($files = scandir($dir))
            {
                $depth += 1;

                for ($i = 0; $i < sizeof($files); ++$i)
                {
                    if ($files[$i] === '.' || $files[$i] === '..')

                        continue;

                    $file = $dir . '/' . $files[$i];

                    is_dir($file) ? $this->scandir($file, $handle, $depth) : call_user_func($handle, $file, $dir, $depth);
                }
            }
        }

        public function filter(callable $filter = null)
        {

        }

        public function output()
        {
            return $this->assets;
        }
    }
}
