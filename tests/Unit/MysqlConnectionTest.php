<?php

use LaravelSpatial\MysqlConnection;
use LaravelSpatial\Schema\Builder;
use Stubs\PDOStub;

class MysqlConnectionTest extends PHPUnit_Framework_TestCase
{
    private $mysqlConnection;

    protected function setUp()
    {
        $mysqlConfig = ['driver' => 'mysql', 'prefix' => 'prefix', 'database' => 'database', 'name' => 'foo'];
        $this->mysqlConnection = new MysqlConnection(new PDOStub(), 'database', 'prefix', $mysqlConfig);
    }

    public function testGetSchemaBuilder()
    {
        $builder = $this->mysqlConnection->getSchemaBuilder();

        $this->assertInstanceOf(Builder::class, $builder);
    }
}
