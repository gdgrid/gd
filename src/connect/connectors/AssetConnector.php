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

    use gdgrid\gd\connect\IConnector;

    use gdgrid\gd\connect\Asset;

    /**
     * show off @property, @property-read, @property-write
     * @property Asset $adapter;
     * */
    class AssetConnector implements IConnector
    {
        use TConnector;

        private $target;

        protected $sources;

        public function init()
        {
            $this->setPushDir(getenv('DOCUMENT_ROOT') . '/gd-assets');

            if (null === $this->sources)

                $this->sources = $this->adapter->fetchSources();
        }

        public function build()
        {
            $this->adapter->buildMode = true;

            $this->adapter->fetchSources();

            $this->adapter->fetchCollector()->build();
        }

        public function setPushDir(string $dir)
        {
            $this->adapter->pushDir = $dir;

            return $this;
        }

        public function sources()
        {
            return $this->sources;
        }

        public function addSources(string $target, string $key, array $sources)
        {
            null == $this->sources ? $this->sources[$target] = [$key => $sources] : $this->sources[$target][$key] = $sources;

            $this->target = $target;

            return $this;
        }

        public function head(array $filter = [])
        {
            $this->sources['head'] = sizeof($filter)

                ? $this->adapter->filterSources($this->sources()['head'], $filter)

                : $this->sources()['head'];

            $this->target = 'head';

            return $this;
        }

        public function end(array $filter = [])
        {
            $this->sources['end'] = sizeof($filter)

                ? $this->adapter->filterSources($this->sources()['end'], $filter)

                : $this->sources()['end'];

            $this->target = 'end';

            return $this;
        }

        public function headCombine(array $filterKeys = [])
        {
            $this->adapter->combineSources($this->sources()['head'], $filterKeys);

            $this->target = 'head';

            return $this;
        }

        public function endCombine(array $filterKeys = [])
        {
            $this->adapter->combineSources($this->sources()['end'], $filterKeys);

            $this->target = 'end';

            return $this;
        }

        public function get(string $view = 'render/asset/sources.php')
        {
            ob_start();

            include $view;

            return ob_get_clean();
        }

        public function __toString()
        {
            return $this->get();
        }

        public function __destruct()
        {
            $this->target = null;
        }
    }
}
