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

namespace gdgrid\gd\bundle
{

    use Exception;
    use Leafo\ScssPhp\Compiler;
    use Patchwork\JSqueeze;

    /**
     * show off @property, @property-read, @property-write
     * */
    class Asset extends AssetBundle
    {
        protected $build = [];

        private static $cssCompiler, $jsCompiler;

        public function __construct(array $sources, string $assetDir)
        {
            parent::__construct($sources, $assetDir);
        }

        public function dispatch()
        {
            $build = array_flip($this->build);

            $err = $push = [];

            foreach (array_merge($this->build, $this->sources) as $item)
            {
                if (isset($push[$item]))

                    continue;

                if (isset($build[$item]))
                {
                    try
                    {
                        if (($compile = $this->build($item)) && $this->push($compile, true))

                            $push[$item] = $compile;
                    }
                    catch (Exception $e)
                    {
                        $err[] = $item . ' (' . $e->getMessage() . ');';
                    }

                    continue;
                }

                if ($path = $this->push($item))

                    $push[$item] = $path;
            }

            if (sizeof($err))

                throw new Exception(sprintf("Asset Compiler: couldn`t compile some sources:\r\n%s", join("\r\n", $err)));

            return $push;
        }

        public function setBuild(array $assets)
        {
            $this->build = $assets;
        }

        public function build(string $source)
        {
            $build = '';

            if ($info = pathinfo($source))
            {
                $build = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.build.' . $info['extension'];

                switch ($info['extension'])
                {
                    case 'css':
                    case 'scss':
                    case 'less':
                        file_put_contents($build, $this->compileCss(file_get_contents($source)));
                        break;
                    case 'js':
                        file_put_contents($build, $this->compileJs(file_get_contents($source)));
                }
            }

            return $build;
        }

        public function push(string $source, bool $replace = false)
        {
            $basePath = '/' . ltrim(str_replace(['/', '\\'], '/', str_replace(dirname($source), '', $source)), '/');

            $targetPath = $this->assetDir . $basePath;

            $pathDir = substr($targetPath, 0, strrpos($targetPath, '/'));

            is_dir($pathDir) or mkdir($pathDir, 0777, true);

            if ($replace ? rename($source, $targetPath) : copy($source, $targetPath))
            {
                chmod($targetPath, 0755);

                return $basePath;
            }

            return false;
        }

        public function compileJs(string $data)
        {
            return self::getJsCompiler()->squeeze(
                $data,
                true,   // singleLine
                false,   // keepImportantComments
                false   // specialVarRx
            );
        }

        public function compileCss(string $data)
        {
            return self::getCssCompiler()->compile($data);
        }

        /**
         * @return Compiler
         */
        public static function getCssCompiler()
        {
            if (self::$cssCompiler === null)
            {
                self::$cssCompiler = new Compiler;

                self::$cssCompiler->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
            }

            return self::$cssCompiler;
        }

        /**
         * @return JSqueeze
         */
        public static function getJsCompiler()
        {
            if (self::$jsCompiler === null)

                self::$jsCompiler = new JSqueeze;

            return self::$jsCompiler;
        }
    }
}
