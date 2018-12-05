<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class DB
{
    private static $capsule;

    /**
     * @return Capsule
     */
    public static function capsule()
    {
        if (static::$capsule === null)
        {
            static::$capsule = new Capsule;

            static::$capsule->addConnection([

                'driver' => 'sqlite',

                'database' => __DIR__ . '/../storage/database.sqlite'

            ]);

            static::$capsule->setAsGlobal();

            static::$capsule->bootEloquent();
        }

        return static::$capsule;
    }
}
