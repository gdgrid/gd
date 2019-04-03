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

        protected $sourceDir = [];

        protected $outputDir;

        protected $compile = false;

        protected $compileIp = ['127.0.0.1', '::1'];

        protected $skipCompileExt = ['php'];

        public function __construct($sourceDir = null, $outputDir = null)
        {
            $this->compile = in_array(getenv('REMOTE_ADDR'), $this->compileIp);

            $this->setSourceDir($sourceDir ?? []);

            $this->setOutputDir($outputDir ?? 'assets');
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

        public function getSourceDir()
        {
            return $this->sourceDir;
        }

        /**
         * @param string $dir
         * @return $this
         */
        public function setOutputDir(string $dir)
        {
            $this->outputDir = getenv('DOCUMENT_ROOT') . '/' . trim($dir, '/');

            return $this;
        }

        public function getOutputDir()
        {
            return $this->outputDir;
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

            $this->outputDir = rtrim($this->outputDir, '/');

            for ($i = 0; $i < sizeof($this->sourceDir); ++$i)
            {
                $this->scandir($this->sourceDir[$i], function($file, $dir) use (
                    $i,
                    $getOutputSubDir,
                    & $outputDir,
                    & $subDir
                )
                {
                    $info = pathinfo($file);

                    if (in_array($info['extension'], $this->skipCompileExt))

                        return false;

                    if (false == isset($subDir[$dir]))
                    {
                        $subDir[$dir] = trim(str_replace($this->sourceDir[$i], '', $dir), '/');

                        if ($getOutputSubDir)

                            $subDir[$dir] = call_user_func($getOutputSubDir, $subDir[$dir]);
                    }

                    if (false == isset($outputDir[$dir]))
                    {
                        $outputDir[$dir] = $this->outputDir . '/' . $subDir[$dir];

                        is_dir($outputDir[$dir]) or mkdir($outputDir[$dir], 0777, true);
                    }

                    $target = $outputDir[$dir] . '/' . $info['basename'];

                    if (false == is_file($target) || (filemtime($file)-filemtime($target)) > 1)

                        copy($file, $target);

                    return true;
                });
            }

            return $this;
        }

        public function scandir(string $dir, callable $handle, bool $recurse = true)
        {
            if ($files = scandir($dir))
            {
                for ($i = 0; $i < sizeof($files); ++$i)
                {
                    if ($files[$i] === '.' || $files[$i] === '..')

                        continue;

                    $file = $dir . '/' . $files[$i];

                    is_dir($file) ? ($recurse ? $this->scandir($file, $handle) : false) : call_user_func($handle, $file, $dir);
                }
            }
        }

        public function output(string $outputDir = null, callable $filter = null)
        {
            $assets = [];

            $this->scandir($outputDir ?? $this->outputDir, function($file, $dir) use (& $assets, $filter)
            {
                if ($filter)
                {
                    if ($item = call_user_func($filter, $file, $dir)) $assets[] = $item;

                    return;
                }

                $assets[] = $this->webPath($file);

            }, false);

            return $assets;
        }

        public function webPath(string $file)
        {
            return str_replace(getenv('DOCUMENT_ROOT'), '', $file);
        }

        public function timestamp(string $assetPath)
        {
            return filemtime(getenv('DOCUMENT_ROOT') . '/' . trim($assetPath, '/'));
        }
    }
}
