<?php

class PostgresTest extends MysqlTest
{
    protected $is_postgres = true;

    /**
     * Boots the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        $app['config']->set('database.default', 'pgsql');
        $app['config']->set('database.connections.pgsql.host', env('DB_HOST', '127.0.0.1'));
        $app['config']->set('database.connections.pgsql.database', 'spatial_test');
        $app['config']->set('database.connections.pgsql.username', 'postgres');
        $app['config']->set('database.connections.pgsql.password', '');

        return $app;
    }

    protected function isMySQL8AfterFix()
    {
        return false;
    }
}
