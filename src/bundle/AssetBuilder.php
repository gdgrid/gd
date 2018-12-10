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
    class AssetBuilder extends AssetBundle
    {
        protected $build = [];

        protected $push = [];

        private static $cssCompiler, $jsCompiler;

        public function dispatch()
        {
            $err = [];

            foreach (array_merge($this->build, $this->sources) as $src => $pushPath)
            {
                if (isset($this->push[$src]))

                    continue;

                if (isset($this->build[$src]))
                {
                    try
                    {
                        if (($compile = $this->build($src)))

                            $this->push[$compile] = $pushPath;
                    }
                    catch (Exception $e)
                    {
                        $err[] = $src . ' (' . $e->getMessage() . ');';
                    }

                    continue;
                }

                $this->push[$src] = $pushPath;
            }

            if (sizeof($err))

                throw new RuntimeException(sprintf("Asset Compiler: can`t compile some sources: \r\n%s", join(";\r\n", $err)));

            return $this;
        }

        public function setBuild(array $sources)
        {
            $this->build = $sources;
        }

        public function build(string $source)
        {
            if ($contents = file_get_contents($source))
            {
                $ext = substr(strrchr($source, '.'), 1);

//                if (false == is_file($sourcePath))
//
//                    return false;
//
//                $path = substr($pushPath, 0, strrpos($pushPath, '.'));
//
//                $ext = substr(strrchr($pushPath, '.'), 1);
//
//                $modify = filemtime($sourcePath);
//
//                $fetchPath = $path . '.' . $modify . ($ext ? '.' . $ext : '');
//
//                return false == is_file($fetchPath) ? $fetchPath : false;

                switch ($ext)
                {
                    case 'css':
                    case 'scss':
                    case 'less':
                        return $this->compileCss($contents);
                        break;
                    case 'js':
                        return $this->compileJs($contents);
                }
            }

            return '';
        }

        public function push(string $source, string $pushPath)
        {
            $err = [];

            foreach ($this->push as $src => $push)
            {
                $pushPathDir = substr(str_replace('\\', '/', $push), 0, strrpos($push, '/'));

                is_dir($pushPathDir) or mkdir($pushPathDir, 0777, true);

                if (copy($src, $push))

                    chmod($push, 0755);

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
