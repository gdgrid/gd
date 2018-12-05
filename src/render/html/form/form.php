<?php

use gdgrid\gd\GridDataFormatter as Format;

/* @var \gdgrid\gd\GridForm $this */

if (null === $this->getTemplate())

    $this->setTemplate('<div {attr}>{label}{input}</div>');

if (false == isset($this->getTagAttributes()['id']))

    $this->setTagAttributes(['id' => ['grid-form-' . substr(md5(microtime(true)), 0, 10)]]);

$output = $this->getLayout() ?: ($this->getTag() ? '<{tag} {attr}>{token}{rows}</{tag}>' : '{token}{rows}');

$rows = '';

foreach ($this->fetchSortOrder() as $k)
{
    if (false == $this->checkInput($k) && false == $this->checkRow($k))

        continue;

    $lt = $this->getLabelTemplate($k);

    $id = $this->checkInput($k) ? $this->getInputID($k) : null;

    if ($ln = $this->getLabel($k))

        $lt = sprintf($lt ?? '<label for="{id}" %s>%s</label>',

            Format::getAttributes($this->getLabelAttributes($k) ?? ['class' => ['control-label']]), $ln);

    $tr = [
        '{attr}'  => $this->getRowAttributes(),
        '{id}'    => $id,
        '{label}' => $lt,
        '{input}' => null,
        '{error}' => null
    ];

    $template = $this->checkRowTemplate($k) ? $this->getRowTemplate($k) : $this->getTemplate();

    if ($this->checkRow($k))
    {
        $row = $this->getRow($k, $tr);

        is_array($row) ? $tr = array_merge($tr, $row) : $tr['{input}'] = $row;
    }

    if (strpos($template, '{label}') !== false)
    {
        $template = strtr($template, ['{label}' => $tr['{label}']]);

        unset($tr['{label}']);
    }

    if (is_array($tr['{attr}']) && false == isset($tr['{attr}']['class']))

        $tr['{attr}']['class'] = ['form-group'];

    if ($this->checkInput($k) && $tr['{input}'] === null)
    {
        $input = null;

        $tpl = '';

        $data = [
            'id'    => $tr['{id}'] ?? $id,
            'type'  => $this->getInputType($k),
            'tag'   => $this->getInput($k)['tag'],
            'attr'  => $this->getInputAttributes($k) ?? [],
            'name'  => $this->getInputRequestName($k),
            'value' => $this->getInputValue($k) ?? $this->getPrompt($k),
            'error' => $tr['{error}'] ?? $this->getError($k),
        ];

        if (false == isset($data['attr']['class']))
        {
            $this->setInputAttribute($k, ['class' => ['form-control']]);

            $data['attr'] = $this->getInputAttributes($k);
        }

        switch ($data['tag'])
        {
            case 'textarea':

                $tpl = '<textarea id="%s" name="%s" %s>%s</textarea>';

                break;

            case 'select':

                $tpl = '<select id="%s" name="%s" %s>%s</select>';

                $value = is_array($data['value']) ? null : $data['value'];

                $data['value'] = '';

                $options = $this->getInputOptions($k);

                if ($prompt = $this->getPrompt($k))
                {
                    $keyPrompt = is_array($prompt) ? key($prompt) : $prompt;

                    if (false == array_key_exists($keyPrompt, $options))
                    {
                        $data['value'] = sprintf('<option value="%s">%s</option>', $keyPrompt, $prompt[$keyPrompt] ?? $keyPrompt);
                    }
                }

                if (sizeof($options) > 0)
                {
                    $opt = '<option value="%s" %s>%s</option>';

                    $vopt = array_keys($options);

                    for ($i = 0; $i < sizeof($vopt); ++$i)
                    {
                        $sel = (string) $vopt[$i] === (string) $value ? 'selected' : null;

                        $data['value'] .= sprintf($opt, $vopt[$i], $sel, $options[$vopt[$i]]);
                    }
                }

                break;

            default:

                $tpl = '<input id="%s" name="%s" %s value="%s">';

                if ($this->isOptionalInput($k))
                {
                    $options = $this->getInputOptions($k);

                    if (empty($options))

                        $options = $data['type'] === 'radio' ? ['No', 'Yes'] : [];

                    $input = [];

                    $vopt = array_keys($options);

                    $tpl = sprintf('<li>%s</li>', $tpl . "\x20" . '%s');

                    $attr = Format::getAttributes(Format::setAttribute($data['attr'], ['class' => ['form-control' => null]]));

                    $value = is_array($data['value']) ? array_flip($data['value']) : [$data['value'] => true];

                    $typeCheckbox = $data['type'] === 'checkbox' && sizeof($vopt) > 1;

                    for ($i = 0; $i < sizeof($vopt); ++$i)
                    {
                        $input[] = sprintf(
                            $tpl,
                            $data['id'] . '-' . $i,
                            ($typeCheckbox ? sprintf('%s[%s]', $data['name'], $i) : $data['name']),
                            $attr . "\x20" . (isset($value[$vopt[$i]]) ? 'checked' : null),
                            $vopt[$i],
                            $options[$vopt[$i]]
                        );
                    }

                    $input = sprintf('<ul class="list-unstyled">%s</ul>', join('', $input));
                }
        }

        $tm = $te = ''; // templates: time input, errors

        if ($data['error'] !== null)
        {
            foreach ((array) $data['error'] as $e)
            {
                $te .= sprintf('<error>%s</error>', $e);
            }

            if (is_array($tr['{attr}']))

                $tr['{attr}'] = Format::setAttribute($tr['{attr}'], ['class' => ['has-error']]);
        }

        if (strpos($data['type'], 'date') !== false && null !== ($time = $this->getInput($k)['time'] ?? null))
        {
            if ($data['type'] === 'datetime-local')
            {
                $data['value'] .= $data['value'] ? "T" . $time : '';
            }
            else
            {
                $attr = array_merge($data['attr'], $this->getInput($k)['attr_time'] ?? []);

                $attr['type'] = 'time';

                $tm = sprintf($tpl, $data['id'] . '-time', $data['name'] . '[time]', Format::getAttributes($attr), $time);

                $data['name'] .= '[date]';

                $data['id'] .= '-date';
            }
        }

        if ($input === null)

            $input = sprintf($tpl, $data['id'], $data['name'], Format::getAttributes($data['attr']), $data['value']);

        $tr['{input}'] = $input . $tm . $te;
    }

    if (is_array($tr['{attr}']))

        $tr['{attr}'] = Format::getAttributes($tr['{attr}']);

    $rows .= strtr($template, $tr);
}

$token = $this->getTokenValue()

    ? sprintf('<input type="hidden" name="%s" value="%s">', $this->getTokenName(), $this->getTokenValue()) : null;

echo strtr($this->fetchLayout($output), [
    '{tag}'   => $this->getTag(),
    '{attr}'  => Format::getAttributes($this->getTagAttributes()),
    '{token}' => $token,
    '{rows}'  => $rows
]);
