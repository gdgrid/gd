<?php

use gdgrid\gd\GridDataFormatter as Format;

/* @var \gdgrid\gd\GridView $this */

$tagAttr = $this->getTagAttributes();

if ($this->getTemplate() === null)
{
    switch ($this->getTag())
    {
        case 'ol':
        case 'ul':

            $t = '<li {attr}>{row}</li>';

            if (false == isset($tagAttr['class']))

                $tagAttr['class'] = ['list-unstyled'];

            break;
        case 'div':

            $t = '<div {attr}>{row}</div>';

            break;
        case 'table':

            $t = '<tr {attr}><td>{row}</td></tr>';

            if (false == isset($tagAttr['class']))

                $tagAttr['class'] = ['table', 'table-striped', 'table-bordered'];

            break;
        default:

            $t = '{row}';
    }

    $this->setTemplate($t);
}

$output = $this->getLayout() ?: ($this->getTag() ? '<{tag} {attr}>{rows}</{tag}>' : '{rows}');

$rows = '';

$attr = Format::getAttributes($this->getRowAttributes());

foreach ($this->getProviderItems() as $i => $item)
{
    $data = $this->fetchData($item, $i);

    $tr = [
        '{attr}' => $attr,
        '{row}'  => null,
    ];

    if (is_array($data))
    {
        if (isset($data['{attr}']) && is_array($data['{attr}']))

            $data['{attr}'] = Format::getAttributes($data['{attr}']);

        $tr = array_merge($tr, $data);
    }
    else
    {
        $tr['{row}'] = $data;
    }

    $rows .= strtr($this->getTemplate(), $tr);
}

echo strtr($this->fetchLayout($output), [
    '{tag}'  => $this->getTag(),
    '{attr}' => Format::getAttributes($tagAttr),
    '{rows}' => $rows
]);
