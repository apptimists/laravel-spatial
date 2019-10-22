<?php

namespace Schema;

use BaseTestCase;
use LaravelSpatial\MysqlConnection;
use LaravelSpatial\PostgresConnection;
use LaravelSpatial\Schema\Blueprint;
use LaravelSpatial\Schema\Builder;
use Mockery;

class BuilderTest extends BaseTestCase
{
    public function testReturnsCorrectBlueprintForMysql()
    {
        $connection = Mockery::mock(MysqlConnection::class);
        $connection->shouldReceive('getSchemaGrammar')->once()->andReturn(null);

        $mock = Mockery::mock(Builder::class, [$connection]);
        $mock->makePartial()->shouldAllowMockingProtectedMethods();
        $blueprint = $mock->createBlueprint('test', function() {
        });

        $this->assertInstanceOf(Blueprint::class, $blueprint);
    }

    public function testReturnsCorrectBlueprintForPostgres()
    {
        $connection = Mockery::mock(PostgresConnection::class);
        $connection->shouldReceive('getSchemaGrammar')->once()->andReturn(null);

        $mock = Mockery::mock(Builder::class, [$connection]);
        $mock->makePartial()->shouldAllowMockingProtectedMethods();
        $blueprint = $mock->createBlueprint('test', function() {
        });

        $this->assertInstanceOf(Blueprint::class, $blueprint);
    }
}
