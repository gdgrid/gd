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

            foreach (glob(GridPlugin::DIR_COMPONENTS . '*') as $dir)
            {
                if (false == is_file($dir . '/assets.json') || false == is_dir($dir . '/assets'))

                    continue;

                if ($data = json_decode(file_get_contents($dir . '/assets.json'), true))
                {
                    $plugin = substr(strrchr($dir, DIRECTORY_SEPARATOR), 1);

                    $this->sources['head'][$plugin] = $this->sources['end'][$plugin] = [];

                    if (false == empty($data['built-head']))

                        $this->sources['head'][$plugin] = $this->setPush($dir . '/assets', $this->pushDir, (array) $data['built-head']);

                    if (false == empty($data['built-end']))

                        $this->sources['end'][$plugin] = $this->setPush($dir . '/assets', $this->pushDir, (array) $data['built-end']);

                    if (false == empty($data['head']))

                        $this->sources['head'][$plugin] = array_merge(
                            $this->sources['head'][$plugin],
                            $this->setBuild($dir . '/assets', $this->pushDir, (array) $data['head'])
                        );

                    if (false == empty($data['end']))

                        $this->sources['end'][$plugin] = array_merge(
                            $this->sources['end'][$plugin],
                            $this->setBuild($dir . '/assets', $this->pushDir, (array) $data['end'])
                        );

                    if (false == empty($data['copy']) && $this->buildMode)

                        $this->fetchCollector()->copy($dir . '/assets', $this->pushDir, (array) $data['copy']);
                }
            }

            dd($this->sources);

            if ($this->buildMode)

                $this->fetchCollector()->build();

            return $this->sources;
        }

        protected function setBuild(string $sourcesDir, string $pushDir, array $sources)
        {
            if ($this->buildMode)

                return $this->fetchCollector()->setBuild($sourcesDir, $pushDir, $sources);

            return $sources;
        }

        protected function setPush(string $sourcesDir, string $pushDir, array $sources)
        {
            if ($this->buildMode)

                return $this->fetchCollector()->setPush($sourcesDir, $pushDir, $sources);

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
