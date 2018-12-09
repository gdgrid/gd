<?php
/**
 * @author GD Lab <dev.gdgrid@gmail.com>
 */

use gdgrid\gd\GridTable;
use gdgrid\gd\GridDataProvider;
use gdgrid\gd\GridData;
use gdgrid\gd\GridForm;
use Illuminate\Http\Request;

$provider = new User;

$items = $provider->filter(Request::capture()->all())->get()->all();

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

    <style>
        .grid-table-filter-submit-buttons button {
            margin: 20px 5px 20px 0
        }
    </style>
</head>
<body>
<div class="container">
    <h1>The Avengers</h1>

    <?php

    $t = microtime(true);

//    \gdgrid\gd\connect\Asset::capture();

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

    # Can Disable the Embed Plugin components:
    #
    # $table->disableEmbedPlugins();

    $table->disableEmbedPlugin('pagination');

    # Can Format the table cells content value:
    #
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

    ?>

    <div>
        <a class="btn btn-success" href="/create">Add Hero</a>
    </div>

    <br><br>

    <?php
    echo '<small>Table generation time: <b>' . (microtime(true) - $t) . '</b> sec</small><br>';
    echo '<small>Page generation time: <b>' . (microtime(true) - APP_START) . '</b> sec</small>';
    ?>

</div>
<script src="/gd-assets/plugin/filter/filter.js"></script>
</body>
</html>
