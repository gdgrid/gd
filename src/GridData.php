<?php
/**
 * Class GridData
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

    use PDO;

    use gdgrid\gd\drv\IGridDataDriver;

    use gdgrid\gd\drv\GridDataMysqlDriver;

    use gdgrid\gd\drv\GridDataSqliteDriver;

    use gdgrid\gd\drv\GridDataPgsqlDriver;

    /**
     * show off @property, @property-read, @property-write
     * @property IGridDataDriver $dataDriver;
     * @property PDO $query;
     * @property PDO $pdo;
     * @method describe(array $bind = []);
     * */
    class GridData implements IGridData
    {
        /*
         * PDO instance
         * */
        protected $pdo;

        /*
         * PDO query instance
         * */
        protected $query;

        /*
         * PDO driver name
         * */
        protected $driver;

        /*
         * Grid data driver components list
         * */
        protected $dataDrivers = [
            'mysql'  => GridDataMysqlDriver::class,
            'sqlite' => GridDataSqliteDriver::class,
            'pgsql'  => GridDataPgsqlDriver::class,
        ];

        /*
         * Grid data driver`s instance
         * */
        protected $dataDriver;

        /*
         * Current Locale
         * */
        protected $locale = 'en';

        /*
         * The Database Table name
         * */
        protected $table;

        /*
         * Current storage.
         *
         * Used to store and access the Database Tables data, retrieved by PDO Statement
         * (tables column info, executed query data and etc.);
         *
         * Notice: You can specify the field name according to the current application locale name,
         * storing it in JSON format on the column comment.
         *
         * Example: [[
         *   "Field" => "username",
         *   "Type" => "varchar(255)",
         *   "Collation" => "utf8mb4_unicode_ci",
         *   "Null" => "NO",
         *   "Key" => "",
         *   "Default" => null,
         *   "Extra" => "",
         *   "Privileges" => "select,insert,update,references",
         *   "Comment" => "{{"name": {"en": "First Name"}}}"
         * ]]
         * */
        protected $storage = [];

        /**
         * @param PDO $pdo
         *
         * @return $this
         */
        public function setPdo(PDO $pdo)
        {
            $this->pdo = $pdo;

            $this->driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

            if (false === $this->getDataDriver() instanceof $this->dataDrivers[$this->driver])

                $this->setDataDriver(new $this->dataDrivers[$this->driver]);

            return $this;
        }

        /**
         * @param PDO $query
         *
         * @return $this
         */
        public function setQuery(PDO $query)
        {
            $this->query = $query;

            return $this;
        }

        /**
         * @return PDO
         */
        public function getPdo()
        {
            return $this->pdo;
        }

        /**
         * @return string
         */
        public function getDriver()
        {
            return $this->driver;
        }

        /**
         * @param IGridDataDriver $dataDriver
         *
         * @return $this
         */
        final function setDataDriver(IGridDataDriver $dataDriver)
        {
            $this->dataDriver = $dataDriver;

            return $this;
        }

        /**
         * @return IGridDataDriver
         */
        public function getDataDriver()
        {
            return $this->dataDriver;
        }

        /**
         * @param string $name
         * @param array $bind
         *
         * @return string
         */
        protected function getSql(string $name, array $bind = [])
        {
            return $this->getDataDriver()->getSqlStatement($name, $bind);
        }

        /**
         * @param string $table
         *
         * @return $this
         */
        public function setTable(string $table)
        {
            $this->table = preg_replace('#[^a-z_\-\s\d\\\'\\"]+#i', '', $table);

            return $this;
        }

        /**
         * @return string
         */
        public function getTable()
        {
            return $this->table;
        }

        /**
         * @param string $group
         * @param string $key
         * @param mixed $data
         *
         * @return $this
         */
        public function setStorage(string $group, string $key, $data)
        {
            if (false == isset($this->storage[$group]))

                $this->storage[$group] = [];

            $this->storage[$group][$key] = $data;

            return $this;
        }

        /**
         * @param string $group
         * @param string $key = null
         *
         * @return mixed
         */
        public function getStorage(string $group, string $key = null)
        {
            return $key !== null ? $this->storage[$group][$key] : $this->storage[$group];
        }

        /**
         * @param string $group
         * @param string $key
         *
         * @return bool
         */
        public function checkStorage(string $group, string $key)
        {
            return isset($this->storage[$group]) && array_key_exists($key, $this->storage[$group]);
        }

        /**
         * The magic method for fetching the Grid Data Driver`s prepared SQL Statement,
         * execute it, put data in storage and return it`s value.
         *
         * @param string $method
         * @param array $arg
         *
         * @return mixed
         */
        public function __call(string $method, array $arg = [])
        {
            if (false == $this->checkStorage($this->getTable(), $method))
            {
                $query = $this->query ?? $this->getPdo()->query($this->getSql($method, [':table' => $this->getTable()]));

                $this->setStorage($this->getTable(), $method, $query->fetchAll(PDO::FETCH_ASSOC));

                $this->query = null;
            }

            return $this->getStorage($this->getTable(), $method);
        }

        /**
         * @param string $locale
         *
         * @return $this
         */
        public function setLocale(string $locale)
        {
            $this->locale = $locale;

            return $this;
        }

        /**
         * @return string
         */
        public function getLocale(): string
        {
            return $this->locale;
        }

        public function fetchFields(): array
        {
            $fields = [];

            foreach ($this->describe() as $field)
            {
                $data = $this->getDataDriver()->fetchColumnData($field);

                $fields[] = [
                    'comment'  => $this->fetchColumnComment($data),
                    'field'    => $this->fetchColumnField($data),
                    'name'     => $this->fetchColumnName($data),
                    'size'     => $this->fetchColumnSize($data),
                    'type'     => $this->fetchColumnType($data),
                    'prompt'   => $this->fetchColumnPrompt($data),
                    'required' => $this->fetchColumnRequired($data),
                ];
            }

            return $fields;
        }

        protected function fetchColumnComment(array $col)
        {
            return json_decode($col['comment'], true) ?: $col['comment'];
        }

        protected function fetchColumnField(array $col)
        {
            return $col['field'];
        }

        protected function fetchColumnName(array $col)
        {
            return $col['comment']['name'][$this->getLocale()] ?? ucfirst(str_replace(['_', '-'], "\x20", $col['field']));
        }

        protected function fetchColumnSize(array $col)
        {
            return $col['size'];
        }

        protected function fetchColumnType(array $col)
        {
            return $col['type'];
        }

        protected function fetchColumnPrompt(array $col)
        {
            return $col['prompt'];
        }

        protected function fetchColumnRequired(array $col)
        {
            return $col['required'];
        }
    }
}
