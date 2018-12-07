<?php
/**
 * @author GD Lab <dev.gdgrid@gmail.com>
 */

require_once 'init.php';

use Seytar\Routing\Router;

use Illuminate\Http\Request;

Router::bootstrap(function($ex)
{
    header('Content-Type: text/html; charset=utf-8');
    echo '404 - Page Not Found';
});

Route::get('/', function()
{
    include 'users.php';
});

Route::get('/delete/{id}', function($id)
{
    if ($user = User::find($id))

        $user->delete();

    return Redirect::to('/');
});

Route::get('/create', function()
{
    define('ROUTE', 'create');

    $provider = new User;

    include 'form.php';
});

Route::get('/update/{id}', function($id)
{
    define('ROUTE', 'update');

    $provider = User::find($id);

    include 'form.php';
});

Route::post('/create', function()
{
    define('ROUTE', 'create');

    $provider = new User;

    $request = Request::capture();

    $validator = (new ValidatorFactory())->make($request->all(), $provider->rules());

    $provider->loadData($request->all());

    if ($validator->fails())
    {
        $provider->setErrors($validator->messages()->toArray());
    }
    else
    {
        $provider->save();

        return Redirect::to('/');
    }

    include 'form.php';
});

Route::post('/update/{id}', function($id)
{
    define('ROUTE', 'update');

    $provider = User::find($id);

    $request = Request::capture();

    $validator = (new ValidatorFactory())->make($request->all(), $provider->rules());

    $provider->loadData($request->all());

    if ($validator->fails())
    {
        $provider->setErrors($validator->messages()->toArray());
    }
    else
    {
        $provider->save();

        return Redirect::to('/');
    }

    include 'form.php';
});
