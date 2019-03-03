# Grid-Data
The PHP 7 Grid-Data Library.

The main purpose of the Library is to automatically generate tables, forms and representations of certain entities in the views.
If the form and column field settings of the entity are not specified, then these settings are taken from the column types and their names in the database table.

For all this, you need to implement a specific interface in the entity itself,
or connect a separate class that implements the interface itself and pass it to the generator.

# Install

``composer require gdgrid/gd``

# Usage example

**1. Using Entity itself.**

``Your model Class:``

```php
<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

use gdgrid\gd\IGridFormProvider;

use gdgrid\gd\IGridTableProvider;

class User extends Eloquent implements IGridFormProvider, IGridTableProvider
{
    protected $fillable = ['name', 'image', 'email', 'gender', 'character'];

    public $timestamps = false;

    protected $errors = [];
    
    ... 
    
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

```

``View File:``

```php
<?php

use gdgrid\gd\GridTable;
use gdgrid\gd\GridForm;

$provider = new User;

$items = $provider->filter(Request::capture()->all())->get()->all();

$table = (new GridTable($provider))->loadColumns();

$table->plugin()->setConfig('bulk-actions', ['view' => false, 'set_query' => false]);

$table->plugin()->hook('filter', function(GridForm $plugin)
{
    $plugin->loadInputs()->setValues(Request::capture()->all());
});

$table->disableEmbedPlugin('pagination');

$table->setProviderItems($items)->setCell('image', function($data)
{
    return $data->image ? '<img src="' . $data->image . '" />' : null;
});

echo $table->render();

```

**2. Using Data Provider.**

``In this case it is not neccessary to implement interfaces in your entity class.``

``Your model Class:``

```php
<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    protected $fillable = ['name', 'image', 'email', 'gender', 'character'];

    public $timestamps = false;

    protected $errors = [];
    
    ... 
}

```

``View File:``

```php
<?php

use gdgrid\gd\GridTable;
use gdgrid\gd\GridDataProvider;
use gdgrid\gd\GridData;
use gdgrid\gd\GridForm;

$provider = new User;

$items = $provider->filter(Request::capture()->all())->get()->all();

$dataProvider = (new GridDataProvider($provider))
    ->setDataProvider((new GridData)
        ->setPdo(DB::capsule()->getConnection()->getPdo())
        ->setTable('users')
        ->setLocale('en'))
    ->fetchData()
    ->setData([
        'safeFields'   => [
            'id',
        ],
        'inputOptions' => [
            'gender' => ['Female', 'Male']
        ]
    ]);

$table = (new GridTable($dataProvider))->loadColumns();

$table->plugin()->setConfig('bulk-actions', ['view' => false, 'set_query' => false]);

$table->plugin()->hook('filter', function(GridForm $plugin)
{
    $plugin->loadInputs()->setValues(Request::capture()->all());
});

# Can Disable the Embedded Plugins:
# $table->disableEmbedPlugins();

# Pagination disabled. To enable it, you must specify quantity of records
# in the "totalCount" configuration parameter:
# $table->plugin()->setConfig('pagination', ['totalCount' => ???]);
$table->disableEmbedPlugin('pagination');

# Can Format the table cells content value:
# $table->setFormatAll(['truncate' => 5]);
# $table->formatter()->mergeFormats([['strtoupper', []]]);
# $table->setFormat([
#     [['name', 'email'], ['trim', 'strip_tags']],
#     ['character', ['strip_html']],
# ]);

$table->setProviderItems($items)->setCell('image', function($data)
{
    return $data->image ? '<img src="' . $data->image . '" />' : null;
});

echo $table->render();

```

The full code of the represented examples you can find in the "testapp" directory of the Library.
Just copy/paste files to the document root of your application.
