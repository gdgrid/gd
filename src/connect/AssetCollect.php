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

    use gdgrid\gd\bundle\AssetBuild;

    /**
     * show off @property, @property-read, @property-write
     * */
    class AssetCollect
    {
        protected $push = [];

        protected $build = [];

        public function setPush(string $sourcesDir, string $pushDir, array $sources)
        {
            return $this->prepare($this->push, $sourcesDir, $pushDir, $sources);
        }

        public function setBuild(string $sourcesDir, string $pushDir, array $sources)
        {
            return $this->prepare($this->build, $sourcesDir, $pushDir, $sources);
        }

        protected function prepare(& $target, string $sourcesDir, string $pushDir, array $sources)
        {
            for ($i = 0; $i < sizeof($sources); ++$i)
            {
                $sources[$i] = '/' . trim($sources[$i], '/..\\');

                if ($path = $this->fetchPushPath($sourcesDir . $sources[$i], $pushDir . $sources[$i]))

                    $target[$sourcesDir . $sources[$i]] = $path;
            }

            return $sources;
        }

        public function copy(string $sourcesDir, string $pushDir, array $sources)
        {
            for ($i = 0; $i < sizeof($sources); ++$i)
            {
                if ($glob = glob($sourcesDir . '/' . ltrim($sources[$i], '/..\\')))
                {
                    for ($ii = 0; $ii < sizeof($glob); ++$ii)
                    {
                        $copy = str_replace($sourcesDir, $pushDir, $glob[$ii]);

                        $copyDir = substr(str_replace('\\', '/', $copy), 0, strrpos($copy, '/'));

                        is_dir($copyDir) or mkdir($copyDir, 0777, true);

                        if (copy($glob[$ii], $copy))
                        {
                            chmod($copyDir, 0755);

                            chmod($copy, 0755);
                        }
                    }
                }
            }
        }

        protected function fetchPushPath(string $source, string $push)
        {
            if (false == is_file($source))

                return false;

            $modify = filemtime($source);

            $info = pathinfo($source);

            $pushDir = substr($push, 0, strrpos($push, DIRECTORY_SEPARATOR)) . '/';

            if (false == is_file($pushDir . $info['filename'] . '.' . $modify . '.' . $info['extension']))

                return $pushDir . $info['filename'] . '.' . $modify . '.' . $info['extension'];

            return false;
        }

        public function build()
        {
            (new AssetBuild($this->push))->setBuild($this->build)->dispatch()->push();
        }
    }
}
