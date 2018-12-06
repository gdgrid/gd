<?php
/**
 * Class AssetAdapter
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd\connect\connectors
{

    use gdgrid\gd\connect\Adapter;
    use gdgrid\gd\connect\IConnector;
    use gdgrid\gd\connect\Asset;
    use RuntimeException;

    /**
     * show off @property, @property-read, @property-write
     * @property $adapter Asset;
     * */
    class AssetConnector implements IConnector
    {
        private $adapter;

        private $target;

        protected $assetDir;

        protected $sources;

        public function init()
        {
            $this->assetDir();

            $this->sources();
        }

        final function attachAdapter(Adapter $adapter): IConnector
        {
            $this->adapter = $adapter;

            return $this;
        }

        /**
         * @return Asset|Adapter
         */
        final function adapter(): Adapter
        {
            return $this->adapter;
        }

        public function setAssetDir(string $dir)
        {
            $this->assetDir = $dir;

            return $this;
        }

        public function assetDir()
        {
            return $this->assetDir ?? $this->adapter()->getAssetDir();
        }

        public function sources()
        {
            return $this->sources ?? $this->adapter()->fetchSources();
        }

        public function addSources(string $target, string $key, array $sources)
        {
            null == $this->sources ? $this->sources = [] : $this->sources[$target][$key] = $sources;

            $this->target = $target;

            return $this;
        }

        public function head(array $filter = [])
        {
            $this->sources['head'] = sizeof($filter)

                ? $this->adapter()->filterSources($this->sources()['head'], $filter)

                : $this->sources()['head'];

            $this->target = 'head';

            return $this;
        }

        public function end(array $filter = [])
        {
            $this->sources['end'] = sizeof($filter)

                ? $this->adapter()->filterSources($this->sources()['end'], $filter)

                : $this->sources()['end'];

            $this->target = 'end';

            return $this;
        }

        public function headCombine(array $filterKeys = [])
        {
            $this->adapter()->combineSources($this->sources()['head'], $filterKeys);

            $this->target = 'head';

            return $this;
        }

        public function endCombine(array $filterKeys = [])
        {
            $this->adapter()->combineSources($this->sources()['end'], $filterKeys);

            $this->target = 'end';

            return $this;
        }

        public function get(string $view = 'render/asset-connector.php')
        {
            ob_start();

            include $view;

            return ob_get_clean();
        }

        public function __destruct()
        {
            $this->target = null;
        }
    }
}
