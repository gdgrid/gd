<?php
/**
 * Interface IGridDataProvider
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd\drv
{
    interface IGridDataDriver
    {
        public function getSqlStatement(string $name, array $bind = []): string;

        public function fetchColumnData(array $column): array;
    }
}
