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
    use gdgrid\gd\bundle\AssetBuilder;
    use gdgrid\gd\plugin\GridPlugin;

    /**
     * show off @property, @property-read, @property-write
     * @method static AssetConnector head();
     * @method static AssetConnector end();
     * */
    class Asset extends Adapter
    {
        use TAdapter;

        protected $storeTime = 60 * 10;

        public $buildMode = false;

        public $pushDir;

        protected $sources = [
            'head' => [],
            'end'  => [],
        ];

        protected $push = [];

        protected $build = [];

        public function fetchAssetBuilder(array $sources)
        {
            return new AssetBuilder($sources);
        }

        public function fetchSources()
        {
            foreach (glob(GridPlugin::DIR_COMPONENTS . '*') as $dir)
            {
                if (false == is_file($dir . '/assets.json') || false == is_dir($dir . '/assets'))

                    continue;

                if ($data = json_decode(file_get_contents($dir . '/assets.json'), true))
                {
                    $plugin = substr(strrchr($dir, DIRECTORY_SEPARATOR), 1);

                    $this->sources['head'][$plugin] = [];

                    if (false == empty($data['built-head']))

                        $this->sources['head'][$plugin] = (array) $data['built-head'];

                    if (false == empty($data['built-end']))

                        $this->sources['end'][$plugin] = (array) $data['built-end'];

                    if (false == empty($data['head']))

                        $this->sources['head'][$plugin] = array_merge($this->sources['head'][$plugin], (array) $data['head']);

                    if (false == empty($data['end']))

                        $this->sources['end'][$plugin] = array_merge($this->sources['end'][$plugin], (array) $data['end']);

                    $this->build($dir . '/assets', $this->sources['head'][$plugin]);

                    $this->build($dir . '/assets', $this->sources['end'][$plugin]);
                }
            }

            return $this->sources;
        }

        protected function build(string $sourcesDir, array $sources)
        {

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
