<?php

/**
 * Class Pagination
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd\plugin\components\pagination
{
    class Pagination
    {
        /**
         * Previous pages.
         */
        const PREVIOUS = 'previous';

        /**
         * Next pages.
         */
        const NEXT = 'next';

        /**
         * Last page.
         */
        const FIRST = 'first';

        /**
         * Last page.
         */
        const LAST = 'last';

        /**
         * @var int Total Items
         */
        protected $totalCount;

        /**
         * @var int Number of the current page
         */
        protected $currentPage;

        /**
         * @var int Items per page
         */
        protected $perPage = 25;

        /**
         * @var int Total pages
         */
        protected $totalPages;

        /**
         * @var int Number of pages at list
         */
        protected $pageSize = 10;

        /**
         * @var string name of page get parameter
         */
        protected $pageName = 'page';

        /**
         * @var string query url string
         */
        protected $queryString = null;

        /**
         * @var string base url
         */
        protected $url = null;

        /**
         * @var array Output array
         */
        protected $pages = [];

        /**
         * @var bool show direct control buttons
         */
        protected $directControls = true;

        /**
         * @var bool show control buttons for first & last pages
         */
        protected $marginControls = true;

        /**
         * @var array control button names
         */
        protected $controls = [
            self::FIRST    => self::FIRST,
            self::LAST     => self::LAST,
            self::PREVIOUS => self::PREVIOUS,
            self::NEXT     => self::NEXT,
        ];

        /**
         * Pagination constructor.
         *
         * @param int $totalCount  Total items
         * @param int $currentPage Number of the current page
         * @param int $perPage     Items per page
         * @param int $pageSize    Number of pages at list
         */
        public function __construct($totalCount, $currentPage = null, $perPage = null, $pageSize = null)
        {
            $this->totalCount = intval($totalCount);

            $this->currentPage = intval($currentPage ?? ($_GET[$this->pageName] ?? 1));

            $this->perPage = intval($perPage ?? $this->perPage);

            $this->pageSize = intval($pageSize ?? $this->pageSize);

            if ($this->perPage <= 0)

                $this->perPage = 1;

            if ($this->currentPage < 1)

                $this->currentPage = 1;

            $this->totalPages = ceil($this->totalCount / $this->perPage);

            if ($this->currentPage > $this->totalPages)

                $this->currentPage = $this->totalPages;
        }

        public function setPageName(string $name)
        {
            $this->pageName = $name;

            return $this;
        }

        public function getPages()
        {
            return $this->build()->pages;
        }

        public function getOffset()
        {
            return ($this->perPage * $this->currentPage - $this->perPage);
        }

        public function getLimit()
        {
            return $this->perPage;
        }

        public function setUrl(string $url = null)
        {
            $this->url = $url ?? parse_url(getenv('REQUEST_URI'))['path'];

            return $this;
        }

        public function getUrl()
        {
            return $this->url;
        }

        public function setQueryString(array $appendData = [])
        {
            $data = [];

            if ($query = parse_url(getenv('REQUEST_URI'))['query'] ?? null)

                parse_str($query, $data);

            $data = array_merge($data, $appendData);

            if (isset($data[$this->pageName]))

                unset($data[$this->pageName]);

            $this->queryString = http_build_query($data);

            return $this;
        }

        public function getQueryString()
        {
            return $this->queryString;
        }

        protected function build()
        {
            if ($this->pages || ($this->totalCount == 0 || $this->totalPages == 1))

                return $this;

            $step = floor($this->pageSize / 2);

            $start = $this->currentPage - $step;

            $end = $this->currentPage + $step;

            if ($this->totalPages > $this->pageSize)
            {
                if ($start <= 1)
                {
                    $start = 1;
                    $end = $this->pageSize;
                }
                if ($end >= $this->totalPages)
                {
                    $end = $this->totalPages;
                    $start = $this->totalPages - ($this->pageSize - 1);
                }
            }
            else
            {
                $start = 1;
                $end = $this->totalPages;
            }

            $q = $this->getQueryString() ?? $this->setQueryString()->getQueryString();

            $url = $this->getUrl() ?? $this->setUrl()->getUrl();

            $this->pages = [
                'pages'       => array_slice(range((int) $start, (int) $end, 1), 0, $this->pageSize),
                'urlQuery'    => $url . ($q ? '?' . $q . '&' : '?'),
                'queryString' => $q,
                'url'         => $url,
                'currentPage' => $this->currentPage,
                'totalCount'  => $this->totalCount,
                'pageName'    => $this->pageName,
                'perPage'     => $this->perPage,
                'offset'      => $this->getOffset(),
                'linkPages'   => [],
                'controls'    => [
                    static::FIRST    => ($end - $this->pageSize > 0 ? 1 : null),
                    static::LAST     => ($start + $end < $this->totalPages ? $this->totalPages : null),
                    static::PREVIOUS => ($this->currentPage > 1 ? $this->currentPage - 1 : null),
                    static::NEXT     => ($this->currentPage < $this->totalPages ? $this->currentPage + 1 : null),
                ],
            ];

            return $this;
        }

        public function fetchPages()
        {
            $this->build();

            if (isset($this->pages['pages']))
            {
                foreach ($this->pages['pages'] as $key => $page)
                {
                    if ($page == $this->pages['currentPage'])
                    {
                        $this->pages['linkPages'][$key] = null;

                        continue;
                    }

                    $this->pages['linkPages'][$key] = $this->pages['urlQuery'] . $this->pageName . '=' . $page;
                }
            }

            return $this;
        }

        public function showDirectControls(bool $flag = null)
        {
            $this->directControls = $flag ?? $this->directControls;

            return $this;
        }

        public function showMarginControls(bool $flag = null)
        {
            $this->marginControls = $flag ?? $this->marginControls;

            return $this;
        }

        public function setControls(array $controls)
        {
            $this->controls = [
                self::FIRST    => $controls[self::FIRST] ?? self::FIRST,
                self::LAST     => $controls[self::LAST] ?? self::LAST,
                self::PREVIOUS => $controls[self::PREVIOUS] ?? self::PREVIOUS,
                self::NEXT     => $controls[self::NEXT] ?? self::NEXT,
            ];

            return $this;
        }

        public function render(array $params = [], string $template = 'pagination-template.php')
        {
            $this->build();

            ob_start();

            foreach ($params as $k => $v)
            {
                $$k = $v;
            }

            unset($params);

            @include($template);

            return ob_get_clean();
        }
    }
}
