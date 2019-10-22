<?php

namespace LaravelSpatial\Schema\Grammars;

use Illuminate\Support\Fluent;
use LaravelSpatial\Schema\Blueprint;

class PostgresGrammar extends \Illuminate\Database\Schema\Grammars\PostgresGrammar
{
    /**
     * Adds a statement to add a point geometry column
     *
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    public function typePoint(Fluent $column)
    {
        return 'GEOGRAPHY(POINT, 4326)';
    }

    /**
     * Adds a statement to add a point geometry column
     *
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    public function typeMultipoint(Fluent $column)
    {
        return 'GEOGRAPHY(MULTIPOINT, 4326)';
    }

    /**
     * Adds a statement to add a polygon geometry column
     *
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    public function typePolygon(Fluent $column)
    {
        return 'GEOGRAPHY(POLYGON, 4326)';
    }

    /**
     * Adds a statement to add a multipolygon geometry column
     *
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    public function typeMultipolygon(Fluent $column)
    {
        return 'GEOGRAPHY(MULTIPOLYGON, 4326)';
    }

    /**
     * Adds a statement to add a linestring geometry column
     *
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    public function typeLinestring(Fluent $column)
    {
        return 'GEOGRAPHY(LINESTRING, 4326)';
    }

    /**
     * Adds a statement to add a multilinestring geometry column
     *
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    public function typeMultilinestring(Fluent $column)
    {
        return 'GEOGRAPHY(MULTILINESTRING, 4326)';
    }

    /**
     * Adds a statement to add a linestring geometry column
     *
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    public function typeGeography(Fluent $column)
    {
        return 'GEOGRAPHY';
    }

    /**
     * Adds a statement to add a geometry geometry column
     *
     * @param Fluent $column
     * @return string
     */
    public function typeGeometry(Fluent $column)
    {
        return 'GEOGRAPHY(GEOMETRY, 4326)';
    }

    /**
     * Adds a statement to add a geometrycollection geometry column
     *
     * @param Fluent $column
     * @return string
     */
    public function typeGeometrycollection(Fluent $column)
    {
        return 'GEOGRAPHY(GEOMETRYCOLLECTION, 4326)';
    }

    /**
     * Adds a statement to create the postgis extension
     *
     * @param Blueprint $blueprint
     * @param Fluent $command
     * @return string
     */
    public function compileEnablePostgis(Blueprint $blueprint, Fluent $command)
    {
        return 'CREATE EXTENSION postgis';
    }

    /**
     * Adds a statement to drop the postgis extension
     *
     * @param Blueprint $blueprint
     * @param Fluent $command
     * @return string
     */
    public function compileDisablePostgis(Blueprint $blueprint, Fluent $command)
    {
        return 'DROP EXTENSION postgis';
    }
}
