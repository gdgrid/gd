<?php
/**
 * Class GridView
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
    class GridView extends Grid
    {
        protected $tag = 'table';

        protected $fetch;

        protected $renderSubDirPath = 'view' . DIRECTORY_SEPARATOR . 'view.php';

        /**
         * GridView constructor.
         *
         * @param IGridProvider $provider
         * @param GridDataFormatter|null $formatter
         */
        public function __construct(IGridProvider $provider, GridDataFormatter $formatter = null)
        {
            parent::__construct($provider);

            $this->setFormatter($formatter ?? new GridDataFormatter);
        }

        public function fetch(callable $fetch)
        {
            $this->fetch = $fetch;

            return $this;
        }

        protected function fetchData($data, $index)
        {
            return call_user_func($this->fetch, $data, $index);
        }

        public function __sleep()
        {
            parent::__sleep();

            $this->fetch = null;
        }
    }
}
