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
        protected $sources = [
            'head' => [],
            'end'  => [],
        ];

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

        public function filterSources(array $sources, array $filterKeys = [])
        {
            if ($filter = array_flip($filter))

                $this->sources['head'] = array_filter($this->sources()['head'], function($key) use ($filter)
                {
                    return isset($filter[$key]);

                }, ARRAY_FILTER_USE_KEY);

            else $this->sources['head'] = $this->sources()['head'];

            $this->target = 'head';
        }

        public function combine(array $sources, array $filterKeys = [])
        {

        }
    }
}
