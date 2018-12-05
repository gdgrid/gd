<?php
/**
 * Interface IGridPlugin
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd\plugin
{
    use gdgrid\gd\Grid;
    
    interface IGridPlugin
    {
        public function __construct(array $components, Grid $instance);
    
        public function setConfig(string $componentName, array $params);
        
        public function setComponents(array $components);

        public function setComponent(string $name, string $class);

        public function setComponentInitPath(array $componentPath);

        public function getComponentInitPath(string $componentName = null);

        public function fetchComponent(string $componentName, callable $fetch);

        public function fetchComponents(array $components = []);

        public function hook(string $componentName, callable $hook);
    }
}
