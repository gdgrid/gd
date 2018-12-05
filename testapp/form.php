<?php
/**
 * @author GD Lab <dev.gdgrid@gmail.com>
 */

use gdgrid\gd\GridForm;
use gdgrid\gd\GridDataProvider;
use gdgrid\gd\GridData;

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
    <h1>Add Hero</h1>

    <?php

    $t = microtime(true);

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
            ],
            'inputErrors'  => $provider->getErrors(),
        ]);

    $form = (new GridForm($dataProvider))->loadInputs();

    $form->setForm(['action' => '/' . ROUTE . '/' . ($provider->id ?? null)]);

    $form->setRequired($dataProvider->requiredFields());

    $form->setRow('_submit_', '<button type="submit" class="btn btn-success">Save</button>');

    echo $form->render();

    ?>

    <br><br>

    <?php
    echo '<small>Form generation time: <b>' . (microtime(true) - $t) . '</b> sec</small><br>';
    echo '<small>Page generation time: <b>' . (microtime(true) - APP_START) . '</b> sec</small>';
    ?>

</div>
</body>
</html>