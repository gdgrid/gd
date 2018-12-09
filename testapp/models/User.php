<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

use gdgrid\gd\IGridFormProvider;

use gdgrid\gd\IGridTableProvider;

class User extends Eloquent implements IGridFormProvider, IGridTableProvider
{
    protected $fillable = ['name', 'image', 'email', 'gender', 'character'];

    public $timestamps = false;

    protected $errors = [];

    protected $filterRules = [
        'name'      => 'like',
        'image'     => 'like',
        'email'     => 'like',
        'gender'    => '=',
        'character' => 'like',
    ];

    public function rules()
    {
        return [
            'name'   => 'required|max:100',
            'email'  => 'email|required|max:100',
            'gender' => 'boolean|required',
        ];
    }

    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function loadData(array $data)
    {
        if ($fillable = $this->fillable)
        {
            foreach ($fillable as $item)
            {
                $this->{$item} = $data[$item] ?? null;
            }

            return true;
        }

        return false;
    }

    public function filter(array $data)
    {
        $query = $this->newQuery();

        foreach ($this->filterRules as $key => $condition)
        {
            if (isset($data[$key]))

                $query->where($key, $condition, $condition === 'like' ? '%' . $data[$key] . '%' : $data[$key]);
        }

        return $query;
    }

    public function gridFields(): array
    {
        return [
            'name' => 'Hero',
            'email' => 'Email',
            'image' => 'Photo',
            'character' => 'Description',
            'gender' => 'Gender',
        ];
    }

    public function gridInputTypes(): array
    {
        return [
            'name' => 'text',
            'email' => 'email',
            'image' => 'text',
            'character' => 'textarea',
            'gender' => 'radio',
        ];
    }

    public function gridInputOptions(): array
    {
        return [
            'gender' => ['Female', 'Male'],
        ];
    }

    public function gridInputSizes(): array
    {
        return [
            'name' => 100,
            'email' => 100,
            'image' => 255,
            'character' => 1000,
        ];
    }

    public function gridSafeFields(): array
    {
        return ['id'];
    }

    public function gridInputErrors(): array
    {
        return $this->errors;
    }

    public function gridTableCellPrompts()
    {
        return '(no data)';
    }

    public function gridInputPrompts(): array
    {
        return [];
    }
}
