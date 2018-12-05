<?php
/**
 * Interface IGridFormProvider
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
    interface IGridFormProvider extends IGridProvider
    {
        public function gridInputTypes(): array;

        public function gridInputSizes(): array;

        public function gridInputOptions(): array;

        public function gridInputPrompts(): array;

        public function gridInputErrors(): array;
    }
}
