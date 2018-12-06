<?php
/**
 * Class GridAdapter
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
    use gdgrid\gd\GridTable;
    
    /**
     * show off @property, @property-read, @property-write
     * */
    class GridConnector implements IConnector
    {
        private $adapter;

        final function attachAdapter(Adapter $adapter): IConnector
        {
            $this->adapter = $adapter;

            return $this;
        }

        final function adapter(): Adapter
        {
            return $this->adapter;
        }

        public function table()
        {
            //
        }
    }
}
