<?php

use gdgrid\gd\IGridFormProvider;
use gdgrid\gd\GridDataFormatter as Format;

/* @var \gdgrid\gd\GridView $this */

if (false == isset($this->getTagAttributes()['id']))

    $this->setTagAttributes(['id' => ['grid-view-' . substr(md5(microtime(true)), 0, 10)]]);

if ($this->getProviderItems() !== null)
{
    include 'view-items.php';

    return;
}

$tagAttr = $this->getTagAttributes();

if ($this->getTemplate() === null)
{
    switch ($this->getTag())
    {
        case 'ol':
        case 'ul':

            $t = '<li {attr}><div>{name}</div><div>{row}</div></li>';

            if (false == isset($tagAttr['class']))

                $tagAttr['class'] = ['list-unstyled'];

            break;
        case 'div':

            $t = '<div {attr}><div>{name}</div><div>{row}</div></div>';

            break;
        case 'table':

            $t = '<tr {attr}><td>{name}</td><td>{row}</td></tr>';

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

$options = $this->getProvider() instanceof IGridFormProvider ? $this->getProvider()->gridInputOptions() : [];

foreach ($this->fetchSortOrder() as $k)
{
    if (false == $this->checkField($k) && false == $this->checkRow($k))

        continue;

    $value = $this->getProviderProperty($k);

    if (is_scalar($value))
    {
        if (isset($options[$k][$value]))

            $value = $options[$k][$value];

        $value = $this->formatter()->format($k, $value)->getValue();
    }

    $tr = [
        '{name}' => $this->getField($k),
        '{attr}' => $attr,
        '{row}'  => $value,
    ];

    if ($this->checkRow($k))
    {
        $row = $this->getRow($k, $tr);

        is_array($row) ? $tr = array_merge($tr, $row) : $tr['{row}'] = $row;
    }

    if ($tr['{name}'] === null)

        $tr['{name}'] = $k;

    if (is_array($tr['{attr}']))

        $tr['{attr}'] = Format::getAttributes($tr['{attr}']);

    if ($tr['{row}'] === null)

        $tr['{row}'] = $this->getPrompt($k) ?? '<div class="no-data">' . $this::NO_DATA . '</div>';

    $rows .= strtr($this->checkRowTemplate($k) ? $this->getRowTemplate($k) : $this->getTemplate(), $tr);
}

echo strtr($this->fetchLayout($output), [
    '{tag}'  => $this->getTag(),
    '{attr}' => Format::getAttributes($tagAttr),
    '{rows}' => $rows
]);
