<?php
/**
 * @author GD Lab <dev.gdgrid@gmail.com>
 */

use gdgrid\gd\bundle\Grid as BundleGrid;
use gdgrid\gd\Grid;
use gdgrid\gd\GridData;
use gdgrid\gd\GridDataProvider;
use gdgrid\gd\GridForm;
use gdgrid\gd\GridTable;
use Illuminate\Http\Request;

$provider = new User;

# The "isStoreOutdated" method checks if the current dataProvider`s instance is outdated in the BundleGrid`s cache:

$items = BundleGrid::capture()->isStoreOutdated('someStoreKey')

    ? $provider->filter(Request::capture()->all())->get()->all() : [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>App Grid</title>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

</head>
<body>
<div class="container">
    <h1>The Avengers</h1>

    <?php

    $t = microtime(true);

    $dataProvider = new GridDataProvider($provider);

    $dataProvider->setDataProvider((new GridData)
        ->setPdo(DB::capsule()->getConnection()->getPdo())
        ->setTable('users')
        ->setLocale('en'));

    $dataProvider->fetchData();

    $dataProvider->mergeData([
        'safeFields'   => [
            'id',
        ],
        'inputOptions' => [
            'gender' => ['Female', 'Male']
        ]
    ]);

    $table = (new GridTable($dataProvider))->loadColumns();

    if (sizeof($items)) $table->setProviderItems($items);

    # Use of the Bundle Grid simplifies all initializations produced above in a single line:
    //    $table = BundleGrid::capture() # method "capture" for create/access the BundleGrid`s singleton.
    //          ->store('someStoreKey') # method "store" (optional) for serialization/access the current GridBundle instance.
    //          ->setProvider($provider)
    //          ->fetchData(DB::capsule()->getConnection()->getPdo(), 'users')
    //          ->mergeData([
    //              'inputOptions' => [
    //                  'gender' => ['FEMALE', 'MALE']
    //              ]
    //          ])->table();

    # Serialize changes in the current BundleGrid`s instance
    # (The methods "store/restore" brings ability for further access the dataProvider`s instance from cache):
    //    if (BundleGrid::capture()->isStoreOutdated('someStoreKey')) BundleGrid::capture()->restore('someStoreKey', 3600);

    $table->plugin()->setConfig('bulk-actions', ['view' => false, 'set_query' => false]);

    $table->plugin()->hook('filter', function(GridForm $plugin, Grid $grid)
    {
        $plugin->loadInputs()->setValues(Request::capture()->all());
    });

    # Can Disable the Embedded Plugins:
    //    $table->disableEmbedPlugins();

    # Pagination disabled. To enable it, you must specify quantity of records
    # in the "totalCount" configuration parameter:
    //    $table->plugin()->setConfig('pagination', ['totalCount' => ???]);

    $table->disableEmbedPlugin('pagination');

    # Can Format the values in the data table cells:
    //    $table->setFormatAll(['truncate' => 5]);
    //    $table->formatter()->mergeFormats([['strtoupper', []]]);
    //    $table->setFormat([
    //        [['name', 'email'], ['trim', 'strip_tags']],
    //        ['character', ['strip_html']],
    //    ]);

    $table->setCell('image', function($data)
    {
        return $data->image ? '<img src="' . $data->image . '" />' : null;
    });

    echo $table->render();

    ?>

    <div>
        <a class="btn btn-success" href="/create">Add Hero</a>
    </div>

    <br><br>

    <?php
    echo '<small>Table generation time: <b>' . (microtime(true)-$t) . '</b> sec</small><br>';
    echo '<small>Page generation time: <b>' . (microtime(true)-APP_START) . '</b> sec</small>';
    ?>

</div>
</body>
</html>
