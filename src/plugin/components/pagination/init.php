<?php

use gdgrid\gd\plugin\components\pagination\Pagination;
use gdgrid\gd\Grid;

/* @var \gdgrid\gd\plugin\GridPlugin $this */

$this->setConfig('pagination', [
    'perPage'  => 25,
    'pageSize' => 10,
]);

$this->fetchComponent('pagination', function(Pagination $plugin, Grid $grid)
{
    $grid->bindLayout('{pagination}', [$plugin->fetchPages()->render(), null, '</{tag}>']);
});
