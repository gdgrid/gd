<?php
/**
 * Class Grid
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd\connect
{
    use gdgrid\gd\connect\connectors\GridConnector;

    class Grid extends Adapter
    {
        use TAdapter;

        public function fetchConnector(): IConnector
        {
            return new GridConnector;
        }
    }
}
