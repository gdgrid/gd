<?php

/**
 * Class GridDataSqliteDriver
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
    class GridDataSqliteDriver implements IGridDataDriver
    {
        /*
         * Prepared SQL Statements
         * */
        protected $sqlStatement = [

            'describe' => 'PRAGMA table_info(\':table\')'

        ];

        /*
         * Indicates which database fields should be used to specify the grid input length attributes according to their type.
         * */
        protected $columnSizeByType = [

            'char', 'varchar', 'text', 'tinytext', 'longtext',

            'mediumtext', 'tinyint', 'smallint'

        ];

        /*
         * Indicates which database fields should be skipped to avoid retrieving their default values according to their type.
         * */
        protected $skipColumnDefaultByType = ['timestamp', 'date', 'time', 'datetime', 'year'];

        /**
         * @param string $name
         * @param array $bind
         *
         * @return string
         */
        public function getSqlStatement(string $name, array $bind = []): string
        {
            return strtr($this->sqlStatement[$name], $bind);
        }

        /**
         * @param array $col
         *
         * @return array
         */
        public function fetchColumnData(array $col): array
        {
            $data = [];

            preg_match('|^([a-z]+)\s*((\()([\d]+)(\)))?\s*([a-z]+)?$|', strtolower($col['type']), $match);

            $data['field'] = $col['name'];

            $data['comment'] = null;

            $data['type'] = $match[1] ?? null;

            $data['size'] = $match[4] ?? null;

            $data['prompt'] = $col['dflt_value'];

            if ($data['size'] && false == in_array($data['type'], $this->columnSizeByType))

                $data['size'] = null;

            if (in_array($data['type'], $this->skipColumnDefaultByType))

                $data['prompt'] = null;

            $data['required'] = $col['notnull'] === '1' && $data['prompt'] === null;

            return $data;
        }
    }
}
