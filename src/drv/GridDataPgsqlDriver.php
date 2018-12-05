<?php

/**
 * Class GridDataPgsqlDriver
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
    class GridDataPgsqlDriver implements IGridDataDriver
    {
        /*
         * Prepared SQL Statements
         * */
        protected $sqlStatement = [

            'describe' =>
            #language=txt
                'SELECT
                    cols.column_name as "field",
                    cols.data_type as "type",
                    cols.character_maximum_length as "size",
                    cols.is_nullable as "null",
                    cols.column_default as "prompt", (
                        SELECT
                            pg_catalog.col_description(c.oid, cols.ordinal_position::int)
                        FROM pg_catalog.pg_class c
                        WHERE
                            c.oid     = (SELECT cols.table_name::regclass::oid) AND
                            c.relname = cols.table_name
                    ) as "comment"
                FROM INFORMATION_SCHEMA.COLUMNS cols WHERE cols.table_name = \':table\''

        ];

        /*
         * Indicates which database fields should be used to specify the Grid Input length attributes according to their type.
         * */
        protected $columnSizeByType = [

            'char', 'varchar', 'text', 'tinytext', 'longtext',

            'mediumtext', 'tinyint', 'smallint', 'character varying'

        ];

        /*
         * Indicates which database fields should be skipped to avoid retrieving their default values according to their type.
         * */
        protected $skipColumnDefaultByType = ['timestamp', 'date', 'time', 'datetime', 'year', 'timestamp with time zone'];

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
            $col['type'] = strtolower(explode("\x20", $col['type'])[0]);

            if ($col['size'] && false == in_array($col['type'], $this->columnSizeByType))

                $col['size'] = null;

            if (($col['type'] === 'integer' && false == is_numeric($col['prompt'])) || in_array($col['type'], $this->skipColumnDefaultByType))

                $col['prompt'] = null;

            $col['required'] = $col['null'] === 'NO' && $col['prompt'] === null;
            
            return $col;
        }
    }
}
