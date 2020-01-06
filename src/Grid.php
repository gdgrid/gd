<?php
/**
 * Class Grid
 *
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

    use gdgrid\gd\plugin\IGridPlugin;

    use gdgrid\gd\plugin\GridPlugin;

    /**
     * show off @property, @property-read, @property-write
     *
     * @property IGridProvider|GridDataProvider $provider;
     * @property GridDataFormatter              $formatter            ;
     * @property IGridPlugin|GridPlugin         $plugin          ;
     * */
    abstract class Grid implements IGrid
    {
        const NO_DATA = '(no data)';

        const RENDER_DIR = __DIR__ . '/render';

        protected $provider;

        protected $providerItems;

        protected $field = [];

        protected $row = [];

        protected $formatter;

        protected $prompt = [];

        protected $renderPath;

        protected $renderSubDir = 'html';

        protected $renderSubDirPath;

        protected $template;

        protected $rowTemplate = [];

        protected $layout;

        protected $bindLayout = [];

        protected $tag;

        protected $tagAttributes = [];

        protected $rowAttributes = [];

        protected $sortOrder = [];

        protected $plugin;

        protected $embedPlugins = [];

        /**
         * Grid constructor.
         *
         * @param IGridProvider|GridDataProvider $provider
         */
        public function __construct(IGridProvider $provider)
        {
            $this->provider = $provider;

            $this->setFields($this->provider->gridFields());
        }

        public function getProvider()
        {
            return $this->provider;
        }

        public function getProviderName()
        {
            return substr(strrchr(get_class($this->getProvider() instanceof GridDataProvider

                ? $this->getProvider()->getEntity() : $this->getProvider()), "\\"), 1);
        }

        public function getProviderProperty(string $property)
        {
            return $this->getProvider()->{$property};
        }

        public function getProviderFormattedProperty(string $property)
        {
            return $this->formatter()->format($property, $this->getProvider()->{$property})->getValue();
        }

        public function getProviderItems()
        {
            if ($this->getProvider() instanceof GridDataProvider)

                return $this->getProvider()->getItems();

            return $this->providerItems;
        }

        public function setProviderItems(array $items)
        {
            if ($this->getProvider() instanceof GridDataProvider)

                $this->getProvider()->setItems($items);

            else $this->providerItems = $items;

            return $this;
        }

        public function setFields(array $data)
        {
            $this->field = array_merge($this->field, array_diff_key($data, array_flip((array)$this->getProvider()->gridSafeFields())));

            return $this;
        }

        public function getFields()
        {
            return $this->field;
        }

        public function getField(string $key)
        {
            return $this->field[$key] ?? null;
        }

        public function checkField(string $key)
        {
            return isset($this->field[$key]);
        }

        public function unsetFields(array $keys)
        {
            foreach ($keys as $key)
            {
                if ($this->checkField($key)) unset($this->field[$key]);
            }

            return $this;
        }

        public function setRow(string $key, $val, string $template = null)
        {
            $this->row[$key] = $val;

            if ($template !== null)

                $this->setTemplate($template, $key);

            return $this;
        }

        public function getRows()
        {
            return $this->row;
        }

        public function checkRow(string $key)
        {
            return isset($this->row[$key]);
        }

        public function getRow(string $key, array $rowData = [])
        {
            if (is_callable($this->row[$key]))

                return call_user_func($this->row[$key], $rowData);

            return $this->row[$key];
        }

        final function setFormatter(GridDataFormatter $formatter)
        {
            $this->formatter = $formatter;

            return $this;
        }

        public function formatter()
        {
            return $this->formatter;
        }

        public function setFormat(array $fieldFormats)
        {
            $this->formatter->setFormat($fieldFormats);

            return $this;
        }

        public function setFormatAll(array $formats)
        {
            $this->formatter->setFormatAll($formats);

            return $this;
        }

        public function setPrompt(string $key, $value)
        {
            $this->prompt[$key] = $value;

            return $this;
        }

        public function setPrompts(array $keyData = [], $prompt = null)
        {
            $prompt = $prompt ?? static::NO_DATA;

            foreach (array_keys($keyData) ?: $this->fetchSortOrder() as $k)
            {
                $this->setPrompt($k, $keyData[$k] ?? $prompt);
            }

            return $this;
        }

        public function getPrompt(string $key)
        {
            return $this->prompt[$key] ?? null;
        }

        public function setRenderPath(string $path)
        {
            $this->renderPath = $path;

            return $this;
        }

        public function getRenderPath()
        {
            return $this->renderPath;
        }

        public function setTemplate(string $template, string $rowKey = null)
        {
            if ($rowKey !== null)
            {
                $this->rowTemplate[$rowKey] = $template;

                return $this;
            }

            $this->template = $template;

            return $this;
        }

        public function getTemplate()
        {
            return $this->template;
        }

        public function checkRowTemplate(string $key)
        {
            return isset($this->rowTemplate[$key]);
        }

        public function getRowTemplate(string $key)
        {
            return $this->rowTemplate[$key] ?? null;
        }

        public function setLayout(string $layout)
        {
            $this->layout = $layout;

            return $this;
        }

        public function getLayout()
        {
            return $this->layout;
        }

        /**
         * @example bindLayout('{some-key}', ['<template></template>', '<{tag}']) - insert template before tag;
         * @example bindLayout('{some-key}', ['<template></template>', null, '</{tag}>']) - insert template after tag;
         *
         * @param string $bindKey
         * @param array  $data
         *
         * @return $this
         */
        public function bindLayout(string $bindKey, array $data)
        {
            $this->bindLayout[$bindKey] = [
                $this->bindLayout[$bindKey][0] ?? ($data[0] ?? null),
                $this->bindLayout[$bindKey][1] ?? ($data[1] ?? null),
                $this->bindLayout[$bindKey][2] ?? ($data[2] ?? null),
            ];

            return $this;
        }

        public function getLayoutBindings()
        {
            return $this->bindLayout;
        }

        protected function fetchLayout(string $layout): string
        {
            $bind = [];

            foreach ($this->bindLayout as $key => $data)
            {
                $bind[$key] = $data[0] ?? null;

                if (strpos($layout, $key) !== false)

                    continue;

                $before = $data[1] ?? null;

                $after = $data[2] ?? null;

                if ($before !== null)

                    $layout = $before === '' ? $key . $layout : str_replace($before, $key . $before, $layout);

                if ($after !== null)

                    $layout = $after === '' ? $layout . $key : str_replace($after, $after . $key, $layout);
            }

            return strtr($layout, $bind);
        }

        public function setTag(string $tag)
        {
            $this->tag = $tag;

            return $this;
        }

        public function getTag(): string
        {
            return $this->tag;
        }

        public function setTagAttributes(array $attr = [])
        {
            $this->tagAttributes = $attr ? GridDataFormatter::setAttribute($this->tagAttributes, $attr) : [];

            return $this;
        }

        public function getTagAttributes()
        {
            return $this->tagAttributes;
        }

        public function setRowAttributes(array $attr = [])
        {
            $this->rowAttributes = $attr ? GridDataFormatter::setAttribute($this->rowAttributes, $attr) : [];

            return $this;
        }

        public function getRowAttributes()
        {
            return $this->rowAttributes;
        }

        public function setSortOrder(array $order)
        {
            $this->sortOrder = array_keys(array_merge(array_flip($order), $this->getFields(), $this->getRows()));

            return $this;
        }

        public function fetchSortOrder()
        {
            return $this->setSortOrder($this->sortOrder)->getSortOrder();
        }

        public function getSortOrder()
        {
            return $this->sortOrder;
        }

        final function setPlugin(IGridPlugin $plugin = null)
        {
            $this->plugin = $plugin ?? new GridPlugin($this->embedPlugins, $this);

            return $this;
        }

        public function plugin()
        {
            return $this->plugin ?? $this->setPlugin()->plugin;
        }

        public function disableEmbedPlugin(string $name)
        {
            $this->embedPlugins = array_diff_key($this->embedPlugins, [$name => true]);

            if (null !== $this->plugin)

                $this->plugin()->unsetComponents([$name]);

            return $this;
        }

        public function disableEmbedPlugins()
        {
            if (null !== $this->plugin)

                $this->plugin()->unsetComponents(array_keys($this->embedPlugins));

            $this->embedPlugins = [];

            return $this;
        }

        public function asHtml()
        {
            $this->renderSubDir = 'html';

            return $this;
        }

        public function asCli()
        {
            $this->renderSubDir = 'cli';

            return $this;
        }

        public function asJson()
        {
            $this->renderSubDir = 'json';

            return $this;
        }

        public function isHtml()
        {
            return $this->renderSubDir === 'html';
        }

        public function isCli()
        {
            return $this->renderSubDir === 'cli';
        }

        public function isJson()
        {
            return $this->renderSubDir === 'json';
        }

        public function render()
        {
            if (null !== $this->plugin || sizeof($this->embedPlugins))

                $this->plugin()->fetchComponents();

            ob_start();

            include($this->getRenderPath() ?? static::RENDER_DIR . '/' . $this->renderSubDir . '/' . $this->renderSubDirPath);

            return ob_get_clean();
        }

        public function __toString()
        {
            return $this->render();
        }

        public function __sleep()
        {
            foreach ($this->row as $key => $row)
            {
                if (is_callable($row)) unset($this->row[$key]);
            }
        }
    }
}
