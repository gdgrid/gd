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

    use gdgrid\gd\bundle\IConnector;
    use gdgrid\gd\IGridFormProvider;
    use gdgrid\gd\IGridProvider;
    use gdgrid\gd\IGridTableProvider;
    use gdgrid\gd\GridForm;
    use gdgrid\gd\GridTable;
    use gdgrid\gd\GridView;
    use gdgrid\gd\bundle\Grid;
    use gdgrid\gd\GridDataProvider;
    use PDO;
    use RuntimeException;

    /**
     * show off @property, @property-read, @property-write
     *
     * @property Grid $adapter;
     * */
    class GridConnector implements IConnector
    {
        use TConnector;

        protected $provider;

        protected $dataProvider;

        protected $data = [];

        protected $table;

        protected $form;

        protected $view;

        protected $attachAssets = true;

        public function setProvider($provider)
        {
            $this->provider = $provider;

            return $this;
        }

        public function setDataProvider(PDO $pdo, string $dataTable, string $locale = null)
        {
            $this->dataProvider = $this->adapter->fetchDataProvider($pdo, $dataTable, $locale);

            return $this;
        }

        protected function fetchDataProvider()
        {
            $this->checkProvider();

            $dataProvider = new GridDataProvider($this->provider);

            if ($this->dataProvider)

                $dataProvider->setDataProvider($this->dataProvider)->fetchData();

            $dataProvider->setData($this->data);

            return $dataProvider;
        }

        public function setData(array $data)
        {
            $this->data = $data;

            return $this;
        }

        public function mergeData(array $data)
        {
            $this->data = array_merge($this->getProviderData($this->provider), $data);

            return $this;
        }

        public function replaceData(array $data)
        {
            $this->mergeData($data);

            foreach ($data as $k => $v)
            {
                if (array_key_exists($k, $this->data)) $this->data[$k] = $v;
            }

            return $this;
        }

        protected function checkProvider()
        {
            if ($this->dataProvider === null && false === $this->provider instanceof IGridProvider)

                throw new RuntimeException(

                    'The "provider" entity must implement both `gdgrid\gd\IGridFormProvider` or `gdgrid\gd\IGridTableProvider` 
                    
                    interfaces or set "dataProvider" instead of that.');

            if ($this->provider === null || get_class($this->provider) === false)

                throw new RuntimeException('The "provider" property must be a valid class object.');
        }

        /**
         * @return GridTable
         */
        public function table()
        {
            return (new GridTable($this->fetchDataProvider()))->loadColumns();
        }

        /**
         * @return GridForm
         */
        public function form()
        {
            return (new GridForm($this->fetchDataProvider()))->loadInputs();
        }

        /**
         * @return GridView
         */
        public function view()
        {
            return (new GridView($this->fetchDataProvider()));
        }

        public function detachAssets()
        {
            $this->attachAssets = false;

            return $this;
        }

        protected function getProviderData($provider)
        {
            $data = [];

            if ($provider instanceof IGridFormProvider)

                $data = [
                    'fields'       => $provider->gridFields(),
                    'safeFields'   => $provider->gridSafeFields(),
                    'inputTypes'   => $provider->gridInputTypes(),
                    'inputSizes'   => $provider->gridInputSizes(),
                    'inputOptions' => $provider->gridInputOptions(),
                    'inputPrompts' => $provider->gridInputPrompts(),
                    'inputErrors'  => $provider->gridInputErrors(),
                ];

            if ($provider instanceof IGridTableProvider)

                $data['tableCellPrompts'] = $provider->gridTableCellPrompts();

            return $data;
        }
    }
}
