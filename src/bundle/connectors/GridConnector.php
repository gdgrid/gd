<?php
/**
 * Class GridConnector
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

namespace gdgrid\gd\bundle\connectors
{

    use gdgrid\gd\bundle\Adapter;

    use gdgrid\gd\bundle\IConnector;

    use gdgrid\gd\GridTable;

    use gdgrid\gd\GridData;

    use PDO;

    /**
     * show off @property, @property-read, @property-write
     * */
    class GridConnector implements IConnector
    {
        use TConnector;

        protected $provider;

        protected $dataProvider;

        private $_data;

        private $_table;

        private $_form;

        private $_view;

        public function setProvider($provider)
        {
            $this->provider = $provider;

            return $this;
        }

        public function setDataProvider(PDO $pdo, string $dataTable, string $locale = null)
        {
            $this->dataProvider = (new GridData)->setPdo($pdo)->setTable($dataTable);

            if ($locale) $this->dataProvider->setLocale($locale);

            return $this;
        }

        public function table($provider = null)
        {
            if ($provider)

                $this->setProvider($provider);


        }

        public function form()
        {
            //
        }

        public function view()
        {
            //
        }
    }
}
