<?php

use Illuminate\Container\Container;
use LaravelSpatial\Connectors\ConnectionFactory;
use LaravelSpatial\MysqlConnection;
use LaravelSpatial\PostgresConnection;
use Stubs\PDOStub;

class ConnectionFactoryBaseTest extends BaseTestCase
{
    public function testMysqlMakeCallsCreateConnection()
    {
        $pdo = new PDOStub();

        $factory = Mockery::mock(ConnectionFactory::class, [new Container()])->makePartial();
        $factory->shouldAllowMockingProtectedMethods();
        $conn = $factory->createConnection('mysql', $pdo, 'database');

        $this->assertInstanceOf(MysqlConnection::class, $conn);
    }

    public function testPostgresMakeCallsCreateConnection()
    {
        $pdo = new PDOStub();

        $factory = Mockery::mock(ConnectionFactory::class, [new Container()])->makePartial();
        $factory->shouldAllowMockingProtectedMethods();
        $conn = $factory->createConnection('pgsql', $pdo, 'database');

        $this->assertInstanceOf(PostgresConnection::class, $conn);
    }

    public function testCreateConnectionDifferentDriver()
    {
        $pdo = new PDOStub();

        $factory = Mockery::mock(ConnectionFactory::class, [new Container()])->makePartial();
        $factory->shouldAllowMockingProtectedMethods();
        $conn = $factory->createConnection('sqlite', $pdo, 'database');

        $this->assertInstanceOf(\Illuminate\Database\SQLiteConnection::class, $conn);
    }
}
