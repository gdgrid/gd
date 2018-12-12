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

                    $this->allocate($this->sources['head'][$plugin], $dir . '/assets', (array) ($data['built-head'] ?? []), true);

                    $this->allocate($this->sources['end'][$plugin], $dir . '/assets', (array) ($data['built-end'] ?? []), true);

                    $this->allocate($this->sources['head'][$plugin], $dir . '/assets', (array) ($data['head'] ?? []));

                    $this->allocate($this->sources['end'][$plugin], $dir . '/assets', (array) ($data['end'] ?? []));

                    if ($this->buildMode && false == empty($data['copy']))

                        $this->fetchCollector()->copy($dir . '/assets', $this->pushDir, (array) $data['copy']);
                }
            }

            return $this->sources;
        }

        protected function allocate(array & $data, string $srcDir, array $sources, bool $push = false)
        {
            $data = $push ? array_merge($data, $this->setPush($srcDir, $this->pushDir, $sources))

                : array_merge($data, $this->setBuild($srcDir, $this->pushDir, $sources));
        }

        protected function setBuild(string $srcDir, string $pushDir, array $sources)
        {
            if ($this->buildMode)

                return $this->fetchCollector()->setBuild($srcDir, $pushDir, $sources);

            return $sources;
        }

        protected function setPush(string $srcDir, string $pushDir, array $sources)
        {
            if ($this->buildMode)

                return $this->fetchCollector()->setPush($srcDir, $pushDir, $sources);

            return $sources;
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
