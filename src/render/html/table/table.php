<?php

use gdgrid\gd\GridDataFormatter as Format;
use gdgrid\gd\IGridFormProvider;
use gdgrid\gd\GridForm;

/* @var \gdgrid\gd\GridTable $this */

if (false == isset($this->getTagAttributes()['id']))

    $this->setTagAttributes(['id' => ['grid-table-' . substr(md5(microtime(true)), 0, 10)]]);

if ($this->getTag() === 'table')
{
    if (false == isset($this->getTagAttributes()['class']))

        $this->setTagAttributes(['class' => ['table', 'table-striped', 'table-bordered']]);

    if (null === $this->getColumnRowTemplate())

        $this->setColumnRowTemplate('<thead><tr {attr}>{columns}</tr></thead>');

    if (null === $this->getTemplate())

        $this->setTemplate('<th {attr}>{column}</th>');

    if (null === $this->getCellRowTemplate())

        $this->setCellRowTemplate('<tr {attr}>{cells}</tr>');

    if (null === $this->getCellTemplate())

        $this->setCellTemplate('<td {attr}>{cell}</td>');
}

$output = $this->getLayout() ?: ($this->getTag() ? '<{tag} {attr}>{columns}{rows}</{tag}>' : '{columns}{rows}');

$columns = '';

$fields = $this->fetchSortOrder();

$sortOrderSize = sizeof($fields);

foreach ($fields as $col)
{
    if (false == $this->checkRow($col))

        continue;

    $template = $this->getRowTemplate($col) ?: $this->getTemplate();

    $columnAttributes = $this->getColumnAttributes($col) ?? [];

    $tr = [
        '{attr}'   => $columnAttributes,
        '{column}' => null,
    ];

    $row = $this->getRow($col, $tr);

    is_array($row) ? $tr = array_merge($tr, $row) : $tr['{column}'] = $row;

    if ($tr['{column}'] === null)

        $tr['{column}'] = $col;

    if (is_array($tr['{attr}']))

        $tr['{attr}'] = Format::getAttributes($tr['{attr}']);

    $columns .= strtr($template, $tr);
}

$columns = str_replace('{columns}', $columns,

    str_replace('{attr}', Format::getAttributes($this->getRowAttributes()), $this->getColumnRowTemplate()));

$rows = '';

$template = $this->getCellTemplate();

if ($this->plugin()->checkFetched('filter') && $this->plugin()->getFetched('filter') instanceof GridForm)
{
    foreach ($this->fetchSortOrder() as $item)
    {
        if ($opt = $this->plugin()->getFetched('filter')->getInputOptions($item))

            $options[$item] = $opt;
    }
}
else $options = $this->getProvider() instanceof IGridFormProvider ? $this->getProvider()->gridInputOptions() : [];

foreach ($this->getProviderItems() as $key => $val)
{
    $cells = '';

    for ($i = 0; $i < $sortOrderSize; ++$i)
    {
        if (false == $this->checkRow($fields[$i]))

            continue;

        $value = $val->{$fields[$i]} ?? ($val[$fields[$i]] ?? null);

        if (is_scalar($value))
        {
            if (isset($options[$fields[$i]][$value]))

                $value = $options[$fields[$i]][$value];

            $value = $this->formatter()->format($fields[$i], $value)->getValue();
        }

        $tr = [
            'template' => null,
            '{attr}'   => $this->getCellAttributes($fields[$i]),
            '{cell}'   => $value,
        ];

        if ($this->checkCell($fields[$i]))
        {
            $row = $this->getCell($fields[$i], $key, $tr);

            is_array($row) ? $tr = array_merge($tr, $row) : $tr['{cell}'] = $row;
        }

        if (is_array($tr['{attr}']))

            $tr['{attr}'] = Format::getAttributes($tr['{attr}']);

        if ($tr['{cell}'] === null)

            $tr['{cell}'] = $this->getPrompt($fields[$i]) ?? '<div class="no-data">' . $this::NO_DATA . '</div>';

        $cells .= strtr($tr['template'] ?? $template, $tr);
    }

    $attr = $this->getCellRowAttributes($key);

    $rows .= str_replace(
        '{cells}',
        $cells,
        str_replace('{attr}', ($attr ? Format::getAttributes($attr) : null), $this->getCellRowTemplate($key))
    );
}

echo strtr($this->fetchLayout($output), [
    '{tag}'     => $this->getTag(),
    '{attr}'    => Format::getAttributes($this->getTagAttributes()),
    '{columns}' => $columns,
    '{rows}'    => $rows,
]);
