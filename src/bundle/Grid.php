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

namespace gdgrid\gd\bundle
{
    use gdgrid\gd\bundle\connectors\GridConnector;

    use gdgrid\gd\GridData;

    use PDO;

    /**
     * show off @property, @property-read, @property-write
     * @mixin GridConnector;
     * */
    class Grid extends Adapter
    {
        use TAdapter;

        public function fetchDataProvider(PDO $pdo, string $dataTable, string $locale = null)
        {
            $provider = (new GridData)->setPdo($pdo)->setTable($dataTable);

            if ($locale) $provider->setLocale($locale);

            return $provider;
        }
    }
}
