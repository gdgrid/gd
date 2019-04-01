<?php

use gdgrid\gd\bundle\connectors\AssetConnector;
use gdgrid\gd\bundle\Asset;
use gdgrid\gd\Grid;

/* @var \gdgrid\gd\plugin\GridPlugin $this */

$this->fetchComponent('assets', function(AssetConnector $plugin, Grid $grid)
{
    $bundle = Asset::setConnector($plugin)->setSourceDir([dirname(dirname(__FILE__))]);

    $bundle->find();

    $grid->bindLayout('{assets}', ["", '<{tag}>']);
    $grid->bindLayout('{/assets}', ["", null, '</{tag}>']);
});
