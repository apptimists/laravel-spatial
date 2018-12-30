<?php

use LaravelSpatial\PostgresConnection;
use LaravelSpatial\Schema\Builder;
use Stubs\PDOStub;

class PostgresConnectionTest extends PHPUnit_Framework_TestCase
{
    private $postgresConnection;

    protected function setUp()
    {
        $pgConfig = ['driver' => 'pgsql', 'prefix' => 'prefix', 'database' => 'database', 'name' => 'foo'];
        $this->postgresConnection = new PostgresConnection(new PDOStub(), 'database', 'prefix', $pgConfig);
    }

    public function testGetSchemaBuilder()
    {
        $builder = $this->postgresConnection->getSchemaBuilder();

        $this->assertInstanceOf(Builder::class, $builder);
    }
}
