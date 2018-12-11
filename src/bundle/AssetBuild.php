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

    use RuntimeException;
    use Exception;
    use Leafo\ScssPhp\Compiler;
    use Patchwork\JSqueeze;

    /**
     * show off @property, @property-read, @property-write
     * */
    class AssetBuild extends AssetBundle
    {
        protected $build = [];

        protected $push = [];

        private static $cssCompiler, $jsCompiler;

        /**
         * @param array $sources
         *
         * @return AssetBuild
         */
        public function setBuild(array $sources)
        {
            $this->build = $sources;

            return $this;
        }

        /**
         * @return AssetBuild
         */
        public function dispatch()
        {
            $err = [];

            foreach (array_merge($this->build, $this->sources) as $src => $push)
            {
                if (isset($this->push[$src]))

                    continue;

                if (isset($this->build[$src]))
                {
                    try
                    {
                        if ($compile = $this->compile($src))

                            $this->push[$compile] = $push;
                    }
                    catch (Exception $e)
                    {
                        $err[] = $src . ' (' . $e->getMessage() . ');';
                    }

                    continue;
                }

                $this->push[$src] = $push;
            }

            if (sizeof($err))

                throw new RuntimeException(sprintf("Asset Compiler: can`t compile some sources: \r\n%s", join(";\r\n", $err)));

            return $this;
        }

        public function compile(string $source)
        {
            $compile = '';

            if ($info = pathinfo($source))
            {
                $compile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.min.' . $info['extension'];

                switch ($info['extension'])
                {
                    case 'css':
                    case 'scss':
                    case 'less':
                        file_put_contents($compile, $this->compileCss(file_get_contents($source)));
                        break;
                    case 'js':
                        file_put_contents($compile, $this->compileJs(file_get_contents($source)));
                }
            }

            return $compile;
        }

        public function push()
        {
            $err = [];

            foreach ($this->push as $src => $push)
            {
                $pushPathDir = substr(str_replace('\\', '/', $push), 0, strrpos($push, '/'));

                is_dir($pushPathDir) or mkdir($pushPathDir, 0777, true);

                if (copy($src, $push))
                {
                    chmod($pushPathDir, 0755);

                    chmod($push, 0755);
                }

                else $err[] = $src;
            }

            if (sizeof($err))

                throw new RuntimeException(sprintf("Asset Pushier: Can`t copy some sources: \r\n%s", join(";\r\n", $err)));

            return false;
        }

        public function compileJs(string $data)
        {
            return self::getJsCompiler()->squeeze(
                $data,
                true,   // singleLine
                false,  // keepImportantComments
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
