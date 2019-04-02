<?php

use gdgrid\gd\bundle\connectors\AssetConnector;
use gdgrid\gd\Grid;

/* @var \gdgrid\gd\plugin\GridPlugin $this */

$this->setConfig('assets', [
    'sourceDir' => [],
    'outputDir' => getenv('DOCUMENT_ROOT') . '/gd-assets',
    'prepend'   => [],
    'append'    => [],
]);

$this->fetchComponent('assets', function(AssetConnector $plugin, Grid $grid)
{
    /* @var \gdgrid\gd\plugin\GridPlugin $this */

    $plugin->setSourceDir(array_merge([dirname(dirname(__FILE__))], $plugin->getSourceDir()));

    $plugin->find(function($subDir)
    {
        return preg_replace('/^([^\/]+)(\/assets)?(.*?)/i', '$1$3', $subDir);
    });

    $prepend = $append = [];

    foreach (array_keys($this->getComponents()) as $component)
    {
        $dirName = dirname($this->getComponentInitPath($component));

        $dir = dirname($dirName);

        $activePlugin = str_replace($dir . '/', '', $dirName);

        $activePluginDir = is_dir($plugin->getOutputDir() . '/' . $activePlugin)

            ? $plugin->getOutputDir() . '/' . $activePlugin : null;

        if ($activePluginDir)

            $plugin->output($activePluginDir, function($file) use ($plugin, & $prepend, & $append)
            {
                $ext = pathinfo($file, PATHINFO_EXTENSION);

                if ($ext === 'css')

                    $prepend[] = $plugin->webPath($file);

                elseif ($ext === 'js')

                    $append[] = $plugin->webPath($file);
            });
    }

    $prepend = array_merge($prepend, (array)$this->getConfig('assets', 'prepend'));

    $append = array_merge($append, (array)$this->getConfig('assets', 'append'));

    $prependHtml = $appendHtml = '';

    if ($sz = sizeof($prepend))
    {
        for ($i = 0; $i < $sz; ++$i)
        {
            $asset = '/' . trim($prepend[$i], '/');

            $prependHtml .= '<link rel="stylesheet" href="'
                . $asset
                . '?v=' . (filemtime(getenv('DOCUMENT_ROOT') . '/' . $asset))
                . '">';
        }
    }

    if ($sz = sizeof($append))
    {
        for ($i = 0; $i < $sz; ++$i)
        {
            $asset = '/' . trim($append[$i], '/');

            $appendHtml .= '<script src="'
                . $asset
                . '?v=' . (filemtime(getenv('DOCUMENT_ROOT') . '/' . $asset))
                . '"></script>';
        }
    }

    $grid->bindLayout('{assets}', [$prependHtml, '<{tag']);

    $grid->bindLayout('{/assets}', [$appendHtml, null, '</{tag}>']);
});
