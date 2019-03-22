<?php
/**
 * Class Grid
 *
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
     *
     * @mixin GridConnector;
     * */
    class Grid extends Adapter
    {
        use TAdapter;

        /**
         * @param PDO         $pdo
         * @param string      $dataTable
         * @param string|null $locale
         * @return GridData
         */
        public function fetchDataProvider(PDO $pdo, string $dataTable, string $locale = null)
        {
            $gd = (new GridData)->setPdo($pdo)->setTable($dataTable);

            if ($locale) $gd->setLocale($locale);

            return $gd;
        }
    }
}
