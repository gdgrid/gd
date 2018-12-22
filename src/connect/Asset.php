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
    use gdgrid\gd\plugin\GridPlugin;

    /**
     * show off @property, @property-read, @property-write
     * @method static AssetConnector head();
     * @method static AssetConnector end();
     * @method static AssetConnector build();
     * */
    class Asset extends Adapter
    {
        use TAdapter;

        public $pushDir;

        public $buildMode = false;

        protected $sources = [];

        protected $collect;

        protected $storeTime = 60 * 10;

        public function fetchCollector()
        {
            return $this->collect ?? new AssetCollect;
        }

        public function fetchSources()
        {
            $this->sources = ['head' => [], 'end' => []];

            foreach (glob(GridPlugin::getDirComponents() . '*') as $dir)
            {
                if (false == is_file($dir . '/assets.json') || false == is_dir($dir . '/assets'))

                    continue;

                if ($data = json_decode(file_get_contents($dir . '/assets.json'), true))
                {
                    $plugin = substr(strrchr($dir, DIRECTORY_SEPARATOR), 1);

                    $this->sources['head'][$plugin] = $this->sources['end'][$plugin] = [];

                    $this->setBuild($this->sources['head'][$plugin], $dir . '/assets', (array) ($data['built-head'] ?? []));

                    $this->setBuild($this->sources['end'][$plugin], $dir . '/assets', (array) ($data['built-end'] ?? []));

                    $this->setPush($this->sources['head'][$plugin], $dir . '/assets', (array) ($data['head'] ?? []));

                    $this->setPush($this->sources['end'][$plugin], $dir . '/assets', (array) ($data['end'] ?? []));

                    if ($this->buildMode && false == empty($data['copy']))

                        $this->fetchCollector()->copy($dir . '/assets', $this->pushDir, (array) $data['copy']);
                }
            }

            return $this->sources;
        }

        protected function setPush(array & $data, string $srcDir, array $sources)
        {
            $data = array_merge($data, $this->buildMode

                ? $this->fetchCollector()->setPush($srcDir, $this->pushDir, $sources) : $sources);
        }

        protected function setBuild(array & $data, string $srcDir, array $sources)
        {
            $data = array_merge($data, $this->buildMode

                ? $this->fetchCollector()->setBuild($srcDir, $this->pushDir, $sources) : $sources);
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
