<?php

use gdgrid\gd\plugin\components\pagination\Pagination;
use gdgrid\gd\Grid;

/* @var \gdgrid\gd\plugin\GridPlugin $this */

$this->setConfig('pagination', [
    'perPage'       => 25,
    'pageSize'      => 10,
    'totalCount'    => 0,
    'insert_before' => null,
    'insert_after'  => '</{tag}>',
]);

$this->fetchComponent('pagination', function(Pagination $plugin, Grid $grid)
{
    $grid->bindLayout('{pagination}', [
        $plugin->fetchPages()->render(),
        $this->getConfig('pagination', 'insert_before'),
        $this->getConfig('pagination', 'insert_after')
    ]);
});
