<?php
/**
 * Class Asset
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
    use gdgrid\gd\bundle\connectors\AssetConnector;

    /**
     * show off @property, @property-read, @property-write
     *
     * @mixin AssetConnector;
     * */
    class Asset extends Adapter
    {
        use TAdapter;
    }
}
