<?php
/**
 * Class Asset
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd\connect
{
    use gdgrid\gd\connect\connectors\AssetConnector;
    use gdgrid\gd\bundle\Asset as AssetBundle;
    use gdgrid\gd\plugin\GridPlugin;

    class Asset extends Adapter
    {
        public function fetchConnector(): IConnector
        {
            return new AssetConnector;
        }

        public function fetchBundle(array $sources, string $assetDir)
        {
            return new AssetBundle($sources, $assetDir);
        }

        public function getAssetDir()
        {
            return getenv('DOCUMENT_ROOT') . DIRECTORY_SEPARATOR . 'gd-grid-assets';
        }

        public function fetchSources()
        {
            return null;
        }
    }
}
