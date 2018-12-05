<?php
/**
 * @author GD Lab <dev.gdgrid@gmail.com>
 */

require 'init.php';

use Illuminate\Database\Capsule\Manager as Capsule;

if (false == Capsule::schema()->hasTable('users'))

    DB::capsule()->getConnection()->getPdo()->exec(
        #language=txt
        'CREATE TABLE "users" (
          "id" integer not null primary key autoincrement, 
          "image" varchar(255), 
          "name" varchar(100) not null, 
          "email" varchar(100) not null, 
          "gender" tinyint(1) not null, 
          "character" text);'
    );

