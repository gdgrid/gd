<?php

use gdgrid\gd\GridForm;
use gdgrid\gd\Grid;

/* @var \gdgrid\gd\plugin\GridPlugin $this */

$this->setConfig('filter', [
    'provider'  => $this->gridObject()->getProvider(),
    'formatter' => $this->gridObject()->formatter(),
    'buttons'   => [
        'submit'   => ['url' => null, 'id' => null, 'attr' => null, 'onclick' => null, 'text' => 'Apply Filter'],
        'reset'    => ['url' => null, 'id' => null, 'attr' => null, 'onclick' => null, 'text' => 'Reset Filter'],
        'template' => null // '{submit} {reset}'
    ]
]);

$this->fetchComponent('filter', function(GridForm $plugin, Grid $grid)
{
    /* @var \gdgrid\gd\plugin\GridPlugin $this */

    if (empty($plugin->getInput()))

        $plugin->loadInputs();

    $id = $plugin->getTagAttributes()['id'] ?? 'grid-table-filter-' . substr(md5(microtime(true)), 0, 10);

    if (empty($plugin->getSortOrder()))

        $plugin->setSortOrder($grid->fetchSortOrder());

    $templateSet = null !== $plugin->getTemplate();

    if ($grid->getTag() === 'table' && ! $templateSet)
    {
        $plugin->setTag('tr')->setTagAttributes([])->setTemplate('<td {attr}>{input}</td>');

        if (false == isset($plugin->getRowAttributes()['class']))

            $plugin->setRowAttributes(['class' => []]);
    }

    $plugin->setTagAttributes(['id' => $id]);

    foreach ($plugin->fetchSortOrder() as $item)
    {
        if (false == $grid->checkRow($item))
        {
            $plugin->unsetInput($item);

            continue;
        }

        if (false == $plugin->checkInput($item) && $grid->checkRow($item))
        {
            $plugin->setRow($item, '');

            continue;
        }

        if (null === $plugin->getRowTemplate($item))
        {
            $type = $plugin->getInputType($item);

            if ($plugin->isOptionalInput($item))
            {
                if ($type !== 'select')

                    $plugin->setSelect($item);

                if ($plugin->getPrompt($item) === null)

                    $plugin->setPrompt($item, ['' => '']);
            }
            elseif ($type === 'date' && isset($plugin->getInput($item)['time']))

                $plugin->setInputType($item, $plugin::DEFAULT_INPUT_TYPE)->setInputAttribute($item, ['data-type' => 'datetime']);

            elseif ($type !== 'number')

                $plugin->setInput($item, null, $plugin::DEFAULT_INPUT_TYPE);
        }
    }

    if ($this->checkConfig('filter', 'buttons') && $buttons = $this->getConfig('filter', 'buttons'))
    {
        $btn = [
            'submit' => [
                'id'      => $buttons['submit']['id'] ?? 'grid-table-filter-submit-' . substr(md5(microtime(true)), 0, 10),
                'text'    => $buttons['submit']['text'] ?? 'Apply Filter',
                'attr'    => $buttons['submit']['attr'] ?? 'class="btn btn-info btn-sm"',
                'onclick' => $buttons['submit']['onclick']
                    ?? sprintf('gdFilterSubmit(\'%s\', \'%s\')', $id, $buttons['submit']['url'] ?? getenv('REQUEST_URI')),
            ],
            'reset'  => [
                'id'      => $buttons['submit']['id'] ?? 'grid-table-filter-reset-' . substr(md5(microtime(true)), 0, 10),
                'text'    => $buttons['reset']['text'] ?? 'Reset Filter',
                'attr'    => $buttons['reset']['attr'] ?? 'class="btn btn-default btn-sm"',
                'onclick' => $buttons['reset']['onclick']
                    ?? sprintf('window.location.href = %s', $buttons['reset']['url'] ?? 'window.location.pathname'),
            ]
        ];

        $template = $buttons['template'] ?? '<div class="grid-table-filter-submit-buttons">{submit} {reset}</div>';

        $template = strtr($template, [

            '{submit}' => sprintf('<button %s id="%s" onclick="%s">%s</button>',

                $btn['submit']['attr'], $btn['submit']['id'], $btn['submit']['onclick'], $btn['submit']['text']),

            '{reset}' => sprintf('<button %s id="%s" onclick="%s">%s</button>',

                $btn['reset']['attr'], $btn['reset']['id'], $btn['reset']['onclick'], $btn['reset']['text'])
        ]);

        if (false == isset($plugin->getTagAttributes()['onkeydown']) && ! $templateSet)

            $plugin->setTagAttributes([
                'onkeydown' => sprintf('if (event.keyCode === 13) $(\'#%s\').trigger(\'click\')', $btn['submit']['id'])
            ]);

        $grid->bindLayout('{filter_btn}', [$template, '<{tag}']);
    }

    $grid->bindLayout('{filter}', [$plugin->render(), null, '{columns}']);

});
