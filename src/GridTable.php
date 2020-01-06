<?php
/**
 * Class GridTable
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

namespace gdgrid\gd
{

    use gdgrid\gd\GridDataFormatter as Format;

    use gdgrid\gd\plugin\components\pagination\Pagination;

    use gdgrid\gd\bundle\connectors\AssetConnector;

    class GridTable extends Grid
    {
        protected $tag = 'table';

        protected $cell = [];

        protected $cellAttributes = ['cell' => [], 'base' => []];

        protected $cellRowAttributes = ['index' => [], 'base' => []];

        protected $cellTemplate;

        protected $cellRowTemplate = ['index' => [], 'base' => null];

        protected $columnAttributes = [];

        protected $columnRowTemplate;

        protected $embedPlugins = [
            'assets'       => AssetConnector::class,
            'bulk-actions' => GridTable::class,
            'filter'       => GridForm::class,
            'pagination'   => Pagination::class,
        ];

        protected $renderSubDirPath = 'table' . DIRECTORY_SEPARATOR . 'table.php';

        /**
         * GridTable constructor.
         *
         * @param IGridTableProvider $provider
         * @param GridDataFormatter  $formatter
         */
        public function __construct(IGridTableProvider $provider, GridDataFormatter $formatter = null)
        {
            parent::__construct($provider);

            $this->setFormatter($formatter ?? new GridDataFormatter);
        }

        public function setTable(array $attr = [])
        {
            $this->setTagAttributes($attr);

            return $this;
        }

        public function loadColumn(string $key, string $name = null)
        {
            $this->row[$key] = $name ?? $key;

            $this->setFields([$key => $this->row[$key]]);

            $this->prompt[$key] = is_array($this->getProvider()->gridTableCellPrompts())

                ? ($this->getProvider()->gridTableCellPrompts()[$key] ?? null) : $this->getProvider()->gridTableCellPrompts();

            return $this;
        }

        public function loadColumns()
        {
            foreach ($this->getFields() as $k => $val)
            {
                $this->loadColumn($k, $val);
            }

            return $this;
        }

        public function unsetColumn(string $key)
        {
            if ($this->checkRow($key))

                unset($this->row[$key], $this->field[$key], $this->prompt[$key]);

            return $this;
        }

        public function unsetColumns(array $keys)
        {
            foreach ($keys as $key)
            {
                $this->unsetColumn($key);
            }

            return $this;
        }

        public function setCell(string $key, $val)
        {
            $this->cell[$key] = $val;

            return $this;
        }

        public function checkCell(string $key)
        {
            return isset($this->cell[$key]);
        }

        public function getCell(string $key, $index = null, array $rowData = [])
        {
            if (is_callable($this->cell[$key]))

                return call_user_func($this->cell[$key], $this->getProviderItems()[$index] ?? null, $index, $rowData);

            return $this->cell[$key];
        }

        public function getColumnKeys()
        {
            return array_keys($this->row);
        }

        public function setCellAttributes(string $cell = null, array $attr = [])
        {
            $cell !== null

                ? $this->cellAttributes['cell'][$cell] = ($attr ? Format::setAttribute($this->getCellAttributes($cell), $attr) : [])

                : $this->cellAttributes['base'] = ($attr ? Format::setAttribute($this->cellAttributes['base'], $attr) : []);

            return $this;
        }

        public function getCellAttributes(string $cell = null)
        {
            return $this->cellAttributes['cell'][$cell] ?? $this->cellAttributes['base'];
        }

        public function setCellRowAttributes(array $attr = [], $index = null)
        {
            $index !== null

                ? $this->cellRowAttributes['index'][$index] = ($attr ? Format::setAttribute($this->getCellRowAttributes($index), $attr) : [])

                : $this->cellRowAttributes['base'] = ($attr ? Format::setAttribute($this->cellRowAttributes['base'], $attr) : []);

            return $this;
        }

        public function getCellRowAttributes($index = null)
        {
            return $this->cellRowAttributes['index'][$index] ?? $this->cellRowAttributes['base'];
        }

        public function setColumnAttributes(string $column, array $attr = [])
        {
            $this->columnAttributes[$column] = $attr ? Format::setAttribute($this->columnAttributes[$column] ?? [], $attr) : [];

            return $this;
        }

        public function getColumnAttributes(string $column)
        {
            return $this->columnAttributes[$column] ?? [];
        }

        public function setColumnRowTemplate(string $template)
        {
            $this->columnRowTemplate = $template;

            return $this;
        }

        public function getColumnRowTemplate()
        {
            return $this->columnRowTemplate;
        }

        public function setCellTemplate(string $template)
        {
            $this->cellTemplate = $template;

            return $this;
        }

        public function getCellTemplate()
        {
            return $this->cellTemplate;
        }

        public function setCellRowTemplate(string $template, $index = null)
        {
            $index !== null ? $this->cellRowTemplate['index'][$index] = $template : $this->cellRowTemplate['base'] = $template;

            return $this;
        }

        public function getCellRowTemplate($index = null)
        {
            return $this->cellRowTemplate['index'][$index] ?? $this->cellRowTemplate['base'];
        }

        public function __sleep()
        {
            parent::__sleep();

            foreach ($this->cell as $key => $cell)
            {
                if (is_callable($cell)) unset($this->cell[$key]);
            }
        }
    }
}
