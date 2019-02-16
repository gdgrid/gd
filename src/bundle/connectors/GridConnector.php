<?php
/**
 * Class GridConnector
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

    use gdgrid\gd\bundle\Adapter;

    use gdgrid\gd\bundle\IConnector;

    use gdgrid\gd\GridTable;

    /**
     * show off @property, @property-read, @property-write
     * */
    class GridConnector implements IConnector
    {
        use TConnector;

        public function table()
        {
            //
        }
    }
}
