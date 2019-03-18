<?php

use gdgrid\gd\bundle\connectors\AssetConnector;
use gdgrid\gd\Grid;

/* @var \gdgrid\gd\plugin\GridPlugin $this */

$this->fetchComponent('assets', function(AssetConnector $plugin, Grid $grid)
{
    $grid->bindLayout('{assets}', ["", '<{tag}>']);
    $grid->bindLayout('{/assets}', ["", null, '</{tag}>']);
});
