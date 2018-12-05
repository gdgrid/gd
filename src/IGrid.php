<?php
/**
 * Interface IGrid
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd
{
    interface IGrid
    {
        /**
         * @param string $path Path to template parent folder
         *
         * @return mixed
         */
        public function setRenderPath(string $path);

        /**
         * @return mixed
         */
        public function render();
    }
}
