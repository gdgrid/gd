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

    /**
     * show off @property, @property-read, @property-write
     * @method static AssetConnector head();
     * @method static AssetConnector end();
     * */
    class Asset extends Adapter
    {
        use TAdapter;

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
            $sources = [];

            foreach (glob(GridPlugin::DIR_COMPONENTS . '*') as $dir)
            {
                $dir = str_replace('\\', '/', $dir);

                if (false == is_file($dir . '/assets.json'))

                    continue;

                $sources[substr($dir, 0, strrpos($dir, '/'))] = json_decode(file_get_contents($dir . '/assets.json'), true) ?: [];
            }

            return $this->dispathSources($sources);
        }

        protected function dispathSources(array $sources)
        {
            $build = [];

            foreach ($sources as $source)
            {

            }
        }

        public function filterSources(array $sources, array $filterKeys = [])
        {
            $filter = array_flip($filterKeys);

            $sources = array_filter($sources, function($key) use ($filter)
            {
                return isset($filter[$key]);

            }, ARRAY_FILTER_USE_KEY);

            return $sources;
        }

        public function combineSources(array $sources, array $filterKeys = [])
        {

        }
    }
}
