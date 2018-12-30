<?php

namespace Eloquent;

use BaseTestCase;
use GeoJson\Geometry\Point;
use GeoJson\Geometry\LineString;
use GeoJson\Geometry\Polygon;
use LaravelSpatial\Eloquent\Builder;
use LaravelSpatial\Eloquent\SpatialTrait;
use LaravelSpatial\MysqlConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Mockery;

class BuilderTest extends BaseTestCase
{
    protected $builder;
    protected $queryBuilder;

    protected function setUp()
    {
        $connection = Mockery::mock(MysqlConnection::class)->makePartial();
        $grammar = Mockery::mock(MySqlGrammar::class)->makePartial();
        $this->queryBuilder = Mockery::mock(QueryBuilder::class, [$connection, $grammar]);

        $this->queryBuilder
            ->shouldReceive('from')
            ->once()
            ->andReturn($this->queryBuilder);

        $this->queryBuilder
            ->shouldReceive('take')
            ->with(1)
            ->andReturn($this->queryBuilder);

        $this->queryBuilder
            ->shouldReceive('get')
            ->andReturn([]);

        $this->builder = new Builder($this->queryBuilder);
        $this->builder->setModel(new TestBuilderModel());
    }

    public function testUpdatePoint()
    {
        $point = new Point([1, 2]);

        $this->queryBuilder
            ->shouldReceive('raw')
            ->with("ST_GeomFromText('POINT (1 2)')")
            ->andReturn(new Expression("ST_GeomFromText('POINT (1 2)')"));

        $this->queryBuilder
            ->shouldReceive('update')
            ->andReturn(1);

        $this->builder->update(['point' => $point]);
    }

    public function testUpdateLinestring()
    {
        $linestring = new LineString([new Point([0, 0]), new Point([1, 1]), new Point([2, 2])]);

        $this->queryBuilder
            ->shouldReceive('raw')
            ->with("ST_GeomFromText('LINESTRING (0 0, 1 1, 2 2)')")
            ->andReturn(new Expression("ST_GeomFromText('LINESTRING (0 0, 1 1, 2 2)')"));

        $this->queryBuilder
            ->shouldReceive('update')
            ->andReturn(1);

        $this->builder->update(['linestring' => $linestring]);
    }

    public function testUpdatePolygon()
    {
        $polygon = new Polygon([[new Point([0, 0]), new Point([0, 1]), new Point([1, 1]), new Point([0, 0])]]);

        $this->queryBuilder
            ->shouldReceive('raw')
            ->with("ST_GeomFromText('POLYGON ((0 0, 0 1, 1 1, 0 0))')")
            ->andReturn(new Expression("ST_GeomFromText('POLYGON ((0 0, 0 1, 1 1, 0 0))')"));

        $this->queryBuilder
            ->shouldReceive('update')
            ->andReturn(1);

        $this->builder->update(['polygon' => $polygon]);
    }
}

class TestBuilderModel extends Model
{
    use SpatialTrait;

    public $timestamps = false;
    protected $spatialFields = ['point', 'linestring', 'polygon'];
}
