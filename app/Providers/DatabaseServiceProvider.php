<?php

namespace App\Providers;

use App\Database\StickySqlConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (config('database.connections.mysql.sticky_by_session')) {
            Connection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
                return new StickySqlConnection($connection, $database, $prefix, $config);
            });
        }
    }
}
