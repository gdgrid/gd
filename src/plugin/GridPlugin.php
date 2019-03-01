<?php
/**
 * Class GridPlugin
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd\plugin
{
    use gdgrid\gd\Grid;
    use RuntimeException;

    /**
     * show off @property, @property-read, @property-write
     * @property Grid $gridObject;
     * */
    class GridPlugin implements IGridPlugin
    {
        protected $components = [];

        protected $gridObject;

        protected $data = [];

        protected $config = [];

        protected $hook = [];

        protected $initPath = [];

        protected $fetched = [];

        protected static $dirComponents = __DIR__ . '/components/';

        public function __construct(array $components, Grid $instance)
        {
            $this->setComponents($components);

            $this->setGridObject($instance);
        }

        public function setComponents(array $components)
        {
            foreach ($components as $name => $class)
            {
                $this->setComponent($name, $class);
            }
    
            return $this;
        }

        public function unsetComponents(array $components)
        {
            $this->components = array_diff_key($this->components, array_flip($components));

            return $this;
        }

        public function setComponent(string $name, string $class)
        {
            $this->components[$name] = $class;

            if (empty($class))

                $this->initPath[$name] = '';

            return $this;
        }

        public function getComponent(string $name)
        {
            return $this->components[$name] ?? null;
        }
    
        public function getComponents()
        {
            return $this->components;
        }

        final function setGridObject(Grid $instance)
        {
            $this->gridObject = $instance;

            return $this;
        }

        public function gridObject()
        {
            return $this->gridObject;
        }

        public function setConfig(string $componentName, array $params)
        {
            $this->config[$componentName] = array_merge($params, $this->config[$componentName] ?? []);

            return $this;
        }

        public function getConfig(string $componentName, string $paramName = null)
        {
            return $paramName ? $this->config[$componentName][$paramName] : ($this->config[$componentName] ?? []);
        }

        public function checkConfig(string $componentName, string $paramName): bool
        {
            return (empty($this->config[$componentName])

                || false == array_key_exists($paramName, $this->config[$componentName])) ? false : true;
        }

        public function setData(string $componentName, array $data)
        {
            $this->data[$componentName] = array_merge($this->data[$componentName] ?? [], $data);

            return $this;
        }

        public function getData(string $componentName)
        {
            return $this->data[$componentName] ?? null;
        }

        /**
         * @param string $componentName
         * @return mixed
         * @throws \ReflectionException
         */
        protected function getComponentInstance(string $componentName)
        {
            $p = [];

            $r = new \ReflectionClass($this->components[$componentName]);

            if ($constructor = $r->getConstructor())
            {
                foreach ($constructor->getParameters() as $param)
                {
                    if (false == $param->isOptional() && false == $this->checkConfig($componentName, $param->name))

                        throw new \logicException

                        (sprintf('The `%s` config parameter have to be set at first in Grid plugin `%s` component.', $param->name, $componentName));

                    $p[] = $this->checkConfig($componentName, $param->name)

                        ? $this->config[$componentName][$param->name] : $param->getDefaultValue();
                }
            }

            return call_user_func_array([$r, 'newInstance'], $p);
        }

        /**
         * @param string   $componentName
         * @param callable $fetch
         * @return $this
         * @throws \ReflectionException
         */
        public function fetchComponent(string $componentName, callable $fetch)
        {
            if (empty($this->getComponent($componentName)) || $this->checkFetched($componentName))

                return $this;

            $instance = $this->components[$componentName] === get_class($this->gridObject)

                ? $this->gridObject : $this->getComponentInstance($componentName);

            if (isset($this->hook[$componentName]))
            {
                foreach ($this->hook[$componentName] as $hook)
                {
                    call_user_func($hook, $instance, $this->gridObject);
                }
            }

            call_user_func($fetch, $instance, $this->gridObject);

            $this->setFetched($componentName, $instance);

            return $this;
        }

        public function fetchComponents(array $components = [])
        {
            foreach ($components ?: array_keys($this->components) as $component)
            {
                if ($path = $this->getComponentInitPath($component))

                    include $path;
            }

            return $this;
        }

        /**
         * @param string $componentName
         * @param        $instance
         * @return $this
         * @throws RuntimeException
         */
        public function setFetched(string $componentName, $instance)
        {
            if (false === $instance instanceof $this->components[$componentName])

                throw new RuntimeException('The plugin component instance must be a valid class object.');

            $this->fetched[$componentName] = $instance;

            return $this;
        }

        public function getFetched(string $componentName)
        {
            return $this->fetched[$componentName];
        }

        public function checkFetched(string $componentName)
        {
            return (false == empty($this->fetched[$componentName]) && gettype($this->fetched[$componentName]) === 'object');
        }

        public function hook(string $componentName, callable $hook)
        {
            if (false == isset($this->hook[$componentName]))

                $this->hook[$componentName] = [];

            $this->hook[$componentName][] = $hook;

            return $this;
        }

        public function setComponentInitPath(array $componentPath)
        {
            foreach ($componentPath as $componentName => $path)
            {
                $this->initPath[$componentName] = $path;
            }

            return $this;
        }

        public function unsetComponentInitPath(array $components)
        {
            $this->initPath = array_diff_key($this->initPath, array_flip($components));

            return $this;
        }

        public function getComponentInitPath(string $componentName = null)
        {
            return $componentName

                ? ($this->initPath[$componentName] ?? static::getDirComponents() . $componentName . '/init.php')

                : $this->initPath;
        }

        static function setDirComponents(string $dir)
        {
            static::$dirComponents = $dir;
        }

        static function getDirComponents()
        {
            return trim(str_replace('\\', '/', static::$dirComponents), '/') . '/';
        }
    }
}
