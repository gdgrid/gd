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

    use gdgrid\gd\GridData;
    use gdgrid\gd\GridDataProvider;
    use gdgrid\gd\GridForm;
    use gdgrid\gd\GridTable;
    use gdgrid\gd\GridView;
    use gdgrid\gd\bundle\Grid;
    use PDO;

    /**
     * show off @property, @property-read, @property-write
     *
     * @property Grid $adapter;
     * @property GridDataProvider $dataProvider;
     * @property GridData $data;
     * */
    class GridConnector implements IConnector
    {
        use TConnector;

        protected $data;

        protected $dataProvider;

        protected $table;

        protected $form;

        protected $view;

        public function setProvider($provider)
        {
            if (isset($this->dataProvider))

                return $this;

            $this->dataProvider = new GridDataProvider($provider);

            return $this;
        }

        public function getProvider()
        {
            return $this->dataProvider;
        }

        public function fetchData(PDO $pdo, string $dataTable, string $locale = null)
        {
            $this->data = $this->adapter->fetchDataProvider($pdo, $dataTable, $locale);

            if ($this->dataProvider instanceof GridDataProvider)

                $this->dataProvider->setDataProvider($this->data)->fetchData();

            return $this;
        }

        /**
         * @return GridData|mixed
         */
        public function getData()
        {
            return $this->data;
        }

        public function setData(array $data)
        {
            $this->dataProvider->setData($data);

            return $this;
        }

        public function mergeData(array $data)
        {
            $this->dataProvider->mergeData($data);

            return $this;
        }

        public function replaceData(array $data)
        {
            $this->dataProvider->replaceData($data);

            return $this;
        }

        /**
         * @return GridTable
         */
        public function table()
        {
            return $this->table ?? $this->table = (new GridTable($this->dataProvider))->loadColumns();
        }

        /**
         * @return GridForm
         */
        public function form()
        {
            return $this->form ?? $this->form = (new GridForm($this->dataProvider))->loadInputs();
        }

        /**
         * @return GridView
         */
        public function view()
        {
            return $this->view ?? $this->view = new GridView($this->dataProvider);
        }
    }
}
