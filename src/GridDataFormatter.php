<?php
/**
 * Class GridDataFormatter
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd
{

    use Exception;

    class GridDataFormatter
    {
        protected $format = [];

        protected $formats = [['enc', []]];

        protected $value;

        /**
         * @param mixed $value
         *
         * @return $this
         * @throws Exception
         */
        public function setValue($value)
        {
            if (false == is_scalar($value))

                throw new Exception(sprintf('The Property `value` can accept only scalar types of value in `%s` class.', __CLASS__));

            $this->value = $value;

            return $this;
        }

        /**
         * @return mixed
         */
        public function getValue()
        {
            return $this->value;
        }

        /**
         * Set multiple array of Grid Fields format.
         *
         * @param array $fieldFormats
         * Example:
         * [
         *  [['username', 'email'], ['trim', 'strip_tags']],
         *  ['character', ['truncate' => 20, 'strip_html']],
         * ]
         *
         * @return $this
         * @throws Exception
         */
        public function setFormat(array $fieldFormats)
        {
            foreach ($fieldFormats as $format)
            {
                if (false == is_array($format))

                    throw new Exception('The format field parameter is not properly defined.');

                $methods = [];

                foreach ((array) $format[1] as $k => $v)
                {
                    if (method_exists($this, $k) || function_exists($k))

                        $methods[] = [$k, (array) $v];

                    elseif (method_exists($this, $v) || function_exists($v))

                        $methods[] = [$v, []];
                }

                foreach ((array) $format[0] as $field)
                {
                    $this->format[$field] = [];

                    foreach ($this->mergeFormats($methods) as $method => $params)
                    {
                        $this->format[$field][] = [$method, $params];
                    }
                }
            }

            return $this;
        }

        /**
         * Set array of formats for all Grid Data Fields.
         *
         * @param array $formats
         * Example:
         * ['strip_html', 'truncate' => [20]]
         *
         * @return $this
         */
        public function setFormatAll(array $formats)
        {
            $this->formats = [];

            foreach ($formats as $k => $v)
            {
                if (method_exists($this, $k) || function_exists($k))

                    $this->formats[] = [$k, (array) $v];

                elseif (method_exists($this, $v) || function_exists($v))

                    $this->formats[] = [$v, []];
            }

            return $this;
        }

        /**
         * Attaches array of formats for all Grid Data Fields.
         *
         * @param array $methods
         * Example:
         * [[trim, []], ['truncate', [20]], ['strip_tags', []]]
         *
         * @return array
         */
        public function mergeFormats(array $methods)
        {
            $formats = [];

            foreach (($this->formats = array_merge($this->formats, $methods)) as $f)
            {
                $formats[$f[0]] = $f[1];
            }

            if (isset($formats['raw']))

                $formats = array_diff_key($formats, ['enc' => true, 'strip_html' => true, 'strip_tags' => true]);

            return $formats;
        }

        /**
         * @param string $field
         * @param mixed $value
         *
         * @return GridDataFormatter
         */
        public function format(string $field, $value)
        {
            $this->value = $value;

            if (isset($this->format[$field]))

                return $this->formatField($this->format[$field]);

            return $this->formatField($this->formats);
        }

        protected function formatField(array $methods)
        {
            for ($i = 0; $i < sizeof($methods); ++$i)
            {
                $this->value = call_user_func_array([$this, $methods[$i][0]], $methods[$i][1]);
            }

            return $this;
        }

        /**
         * @param int $size      = 20
         * @param string $suffix = '...'
         *
         * @return string
         */
        public function truncate(int $size = 20, string $suffix = '...')
        {
            $words = preg_split('/(\s+)/u', trim($this->value), null, PREG_SPLIT_DELIM_CAPTURE);

            if (sizeof($words) / 2 > $size)

                return implode('', array_slice($words, 0, ($size * 2) - 1)) . $suffix;

            return $this->value;
        }

        /**
         * @param string $format
         *
         * @return false|string
         */
        public function date($format = 'm/d/yyyy')
        {
            return date($format, strtotime($this->value));
        }

        /**
         * @return string
         */
        public function raw()
        {
            return html_entity_decode($this->value, ENT_QUOTES);
        }

        /**
         * @return string
         */
        public function enc()
        {
            return htmlentities($this->value, ENT_QUOTES);
        }

        /**
         * Removes all HTML tags, javascript sections, whitespace characters.
         * It is also necessary to replace some HTML entities at their equivalent.
         *
         * @param bool $nl2br = true
         *
         * @return string
         */
        public function strip_html(bool $nl2br = true)
        {
            $search = [
                "'<script[^>]*?>.*?</script>'si",  # cut javaScript
                "'<[\/\!]*?[^<>]*?>'si",           # cut HTML-tags
                "'([\r\n])[\s]+'",                 # cut whitespace characters
                "'&(quot|#34);'i",                 # replace HTML-entities
                "'&(amp|#38);'i",
                "'&(lt|#60);'i",
                "'&(gt|#62);'i",
                "'&(nbsp|#160);'i",
                "'&(iexcl|#161);'i",
                "'&(cent|#162);'i",
                "'&(pound|#163);'i",
                "'&(copy|#169);'i",
            ];

            $replace = ["", "", "\\1", "\"", "&", "<", ">", " ", chr(161), chr(162), chr(163), chr(169)];

            $this->value = preg_replace($search, $replace, $this->value);

            return $nl2br ? $this->value = nl2br($this->value) : $this->value;
        }

        /**
         * Convert Camel case to dash case.
         *
         * @param string $name
         *
         * @return string
         */
        public static function dashName(string $name)
        {
            return trim(strtolower(preg_replace('/([A-Z])/', '-$1', $name)), '-');
        }

        /**
         * Set array of html tag attributes, unset if attribute value is empty.
         *
         * @param array $src
         * @param array $attr
         *
         * @return array
         */
        public static function setAttribute(array $src, array $attr)
        {
            foreach ($attr as $k => $v)
            {
                if ($v === '' || $v === false || $v === null)
                {
                    if (isset($src[$k]))

                        unset($src[$k]);

                    continue;
                }

                if (is_array($v))
                {
                    $src[$k] = false == isset($src[$k])

                        ? $v : array_merge(is_array($src[$k]) ? $src[$k] : explode("\x20", $src[$k]), $v);

                    foreach ($src[$k] as $kk => $vv)
                    {
                        if ($vv === '' || $vv === false || $vv === null)
                        {
                            if (($key = array_search($kk, $src[$k])) !== false)

                                unset($src[$k][$key]);

                            unset($src[$k][$kk]);
                        }
                    }

                    continue;
                }

                $src[$k] = $v;
            }

            return $src;
        }

        /**
         * Convert Array of html tag attributes to string.
         *
         * @param array $src
         *
         * @return string
         */
        public static function getAttributes(array $src): string
        {
            $output = [];

            foreach ($src as $k => $v)
            {
                $inn = [];

                if (is_array($v))
                {
                    foreach ($v as $kk => $vv)
                    {
                        switch ($k)
                        {
                            case 'data':

                                $output[] = sprintf('data-%s="%s"', $kk, $vv);

                                break;

                            case 'style':

                                $inn[] = sprintf('%s:%s;', $kk, $vv);

                                break;

                            default:

                                $inn[] = $vv;
                        }
                    }

                    if ($k === 'data') continue;
                }

                $output[] = sprintf('%s="%s"', $k, $inn ? join("\x20", $inn) : (is_array($v) ? join("\x20", $v) : $v));
            }

            return join("\x20", $output);
        }

        public function __call(string $m, array $args = [])
        {
            if (false == function_exists($m))

                throw new Exception(sprintf('The method or function `%s` not found in `%s` class.'), $m, __CLASS__);

            return call_user_func_array($m, array_merge([$this->value], $args));
        }

        public function __destruct()
        {
            $this->value = null;
        }
    }
}
