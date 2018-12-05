<?php

use gdgrid\gd\plugin\components\pagination\Pagination;

/* @var \gdgrid\gd\plugin\GridPlugin $this */

$this->setConfig('pagination', [
    'perPage'  => 25,
    'pageSize' => 10,
]);

$this->fetchComponent('pagination', function(Pagination $plugin)
{
    $this->gridObject()->bindLayout('{pagination}', [$plugin->fetchPages()->render(), null, '</{tag}>']);
});
