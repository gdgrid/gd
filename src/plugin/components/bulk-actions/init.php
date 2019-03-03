<?php

use gdgrid\gd\GridTable;

/* @var \gdgrid\gd\plugin\GridPlugin $this */

$this->setConfig('bulk-actions', [
    'set_query'      => true,
    'action_columns' => [
        'view'   => ['column' => 'bulk_action_view', 'name' => null, 'action_field' => null, 'template' => '{view}'],
        'update' => ['column' => 'bulk_action_update', 'name' => null, 'action_field' => null, 'template' => '{update}'],
        'delete' => ['column' => 'bulk_action_delete', 'name' => null, 'action_field' => null, 'template' => '{delete}'],
    ],
    'action_field'   => 'id',
    'template'       => '{view} {update} {delete}',
    'view'           => [
        'template' => '<a href="%s%s" %s>%s</a>',
        'url'      => null,
        'attr'     => null,
        'text'     => '<i class="glyphicon glyphicon-eye-open"></i>'
    ],
    'update'         => [
        'template' => '<a href="%s%s" %s>%s</a>',
        'url'      => null,
        'attr'     => null,
        'text'     => '<i class="glyphicon glyphicon-pencil"></i>'
    ],
    'delete'         => [
        'template' => '<a href="%s%s" %s>%s</a>',
        'url'      => null,
        'attr'     => 'onclick="if (false == confirm(\'Are you sure you want to delete this element?\')) return false"',
        'text'     => '<i class="glyphicon glyphicon-trash"></i>'
    ],
]);

$this->fetchComponent('bulk-actions', function(GridTable $plugin)
{
    $params = $this->getConfig('bulk-actions');

    $url = rtrim(parse_url(getenv('REQUEST_URI'))['path'], '/');

    $field = $params['action_field'];

    $setQuery = false == empty($params['set_query']);

    $template = $params['template'];

    $actions = [
        'view'   => sprintf($params['view']['template'],
            $params['view']['url'] ?? $url . '/view',
            $setQuery ? '?id={item_id}' : '/{item_id}',
            $params['view']['attr'],
            $params['view']['text']
        ),
        'update' => sprintf($params['update']['template'],
            $params['update']['url'] ?? $url . '/update',
            $setQuery ? '?id={item_id}' : '/{item_id}',
            $params['update']['attr'],
            $params['update']['text']
        ),
        'delete' => sprintf($params['delete']['template'],
            $params['delete']['url'] ?? $url . '/delete',
            $setQuery ? '?id={item_id}' : '/{item_id}',
            $params['delete']['attr'],
            $params['delete']['text']
        ),
    ];

    $columns = [];

    $sortOrder = $plugin->fetchSortOrder();

    foreach ($params['action_columns'] as $action => $col)
    {
        $column = $col['column'] ?? 'bulk_action_' . $action;

        if (empty($params[$action]) || $plugin->checkRow($column))

            continue;

        if (false == in_array($column, $sortOrder))

            $columns[] = $column;

        $tpl = $col['template'] ?? $template;

        $field = $col['action_field'] ?? $field;

        $plugin->loadColumn($column, $col['name'] ?? '');

        $tpl = str_replace('{view}', $actions['view'], $tpl);
        $tpl = str_replace('{update}', $actions['update'], $tpl);
        $tpl = str_replace('{delete}', $actions['delete'], $tpl);

        $plugin->setCell($column, function($data) use ($tpl, $field)
        {
            return str_replace('{item_id}', $data->{$field} ?? ($data[$field] ?? null), $tpl);
        });

        if (false == isset(
                $params['view']['text'],
                $plugin->getColumnAttributes($column)['class'],
                $plugin->getColumnAttributes($column)['style'])
        )

            $plugin->setColumnAttributes($column, ['style' => ['width' => '20px']]);
    }

    $plugin->setSortOrder(array_merge($columns, $sortOrder));

});
