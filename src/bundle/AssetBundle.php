<?php
/**
 * Class AssetBundle
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd\bundle
{
    /**
     * show off @property, @property-read, @property-write
     * */
    abstract class AssetBundle
    {
        protected $sources = [];

        protected $assetDir;

        public function __construct(array $sources, string $assetDir)
        {
            $this->sources = $sources;

            $this->assetDir = rtrim($assetDir, '/..\\');
        }

        abstract function dispatch();
    }
}
