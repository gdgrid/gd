<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
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
}
