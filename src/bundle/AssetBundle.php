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

        public function __construct(array $sources)
        {
            $this->sources = $sources;
        }

        abstract function dispatch();

        abstract function compile(string $source);

        abstract function push();
    }
}
