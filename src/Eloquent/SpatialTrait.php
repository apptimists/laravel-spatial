<?php

namespace LaravelSpatial\Eloquent;

use GeoJson\GeoJson;
use GeoJSON\Geometry\Geometry;
use geoPHP\geoPHP;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use LaravelSpatial\Exceptions\SpatialFieldsNotDefinedException;
use LaravelSpatial\Exceptions\UnknownSpatialRelationFunction;

/**
 * Trait SpatialTrait.
 *
 * @property array $attributes
 * @method static distance($geometryColumn, $geometry, $distance)
 * @method static distanceExcludingSelf($geometryColumn, $geometry, $distance)
 * @method static distanceSphere($geometryColumn, $geometry, $distance)
 * @method static distanceSphereExcludingSelf($geometryColumn, $geometry, $distance)
 * @method static comparison($geometryColumn, $geometry, $relationship)
 * @method static within($geometryColumn, $polygon)
 * @method static crosses($geometryColumn, $geometry)
 * @method static contains($geometryColumn, $geometry)
 * @method static disjoint($geometryColumn, $geometry)
 * @method static equals($geometryColumn, $geometry)
 * @method static intersects($geometryColumn, $geometry)
 * @method static overlaps($geometryColumn, $geometry)
 * @method static doesTouch($geometryColumn, $geometry)
 */
trait SpatialTrait
{
    /*
     * The attributes that are spatial representations.
     * To use this Trait, add the following array to the model class
     *
     * @var array
     *
     * protected $spatialFields = [];
     */

    public $geometries = [];

    protected $stRelations = [
        'Within',
        'Crosses',
        'Contains',
        'Disjoint',
        'Equals',
        'Intersects',
        'Overlaps',
        'Touches',
    ];

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \LaravelSpatial\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    public function setRawAttributes(array $attributes, $sync = false)
    {
        $spatial_fields = $this->getSpatialFields();

        foreach ($attributes as $attribute => &$value) {
            if (in_array($attribute, $spatial_fields) && is_string($value) && strlen($value) >= 15) {
                // MySQL adds 4 NULL bytes at the start of the binary
                if ($this->getConnection() instanceof MySqlConnection && substr($value, 0, 4) == "\0\0\0\0") {
                    $value = substr($value, 4);
                } elseif ($this->getConnection() instanceof PostgresConnection) {
                    $value = pack('H*', $value);
                }

                try {
                    $value = GeoJson::jsonUnserialize(json_decode(geoPHP::load($value, 'wkb')->out('json')));
                } catch (\Exception $e) {
                    throw new \Exception("Can't parse WKB {$value}: {$e->getMessage()}", $e->getCode(), $e);
                }
            }
        }

        parent::setRawAttributes($attributes, $sync);
    }

    public function getSpatialFields()
    {
        if (property_exists($this, 'spatialFields') && !empty($this->spatialFields)) {
            return $this->spatialFields;
        } else {
            throw new SpatialFieldsNotDefinedException(__CLASS__ . ' has to define $spatialFields');
        }
    }

    protected function toWkt(Geometry $value)
    {
        return ($this->getConnection() instanceof PostgresConnection ? 'SRID=4326;' : '') .
                geoPHP::load(json_decode(json_encode($value->jsonSerialize()), false), 'json')->out('wkt');
    }

    protected function performInsert(EloquentBuilder $query, array $options = [])
    {
        foreach ($this->attributes as $key => $value) {
            if ($value instanceof Geometry && $this->isColumnAllowed($key)) {
                $this->geometries[$key] = $value; // Preserve the geometry objects prior to the insert
                $this->attributes[$key] = $this->getConnection()->raw("ST_GeomFromText('{$this->toWkt($value)}')");
            }
        }

        $insert = parent::performInsert($query, $options);

        foreach ($this->geometries as $key => $value) {
            $this->attributes[$key] = $value; // Retrieve the geometry objects so they can be used in the model
        }

        return $insert; // Return the result of the parent insert
    }

    public function isColumnAllowed($geometryColumn)
    {
        if (!in_array($geometryColumn, $this->getSpatialFields())) {
            throw new SpatialFieldsNotDefinedException();
        }

        return true;
    }

    public function scopeDistance($query, $geometryColumn, $geometry, $distance)
    {
        if ($this->isColumnAllowed($geometryColumn)) {
            $geometryColumn .= $this->getConnection() instanceof PostgresConnection ? '::geometry' : '';
            $query->whereRaw("ST_Distance({$geometryColumn}, ST_GeomFromText(?)) <= ?", [
                $this->toWkt($geometry),
                $distance,
            ]);
        }

        return $query;
    }

    public function scopeDistanceExcludingSelf($query, $geometryColumn, $geometry, $distance)
    {
        if ($this->isColumnAllowed($geometryColumn)) {
            $query = $this->scopeDistance($query, $geometryColumn, $geometry, $distance);

            $geometryColumn .= $this->getConnection() instanceof PostgresConnection ? '::geometry' : '';
            $query->whereRaw("ST_Distance({$geometryColumn}, ST_GeomFromText(?)) != 0", [
                $this->toWkt($geometry),
            ]);
        }

        return $query;
    }

    public function scopeDistanceValue($query, $geometryColumn, $geometry)
    {
        if ($this->isColumnAllowed($geometryColumn)) {
            $columns = $query->getQuery()->columns;

            if (!$columns) {
                $query->select('*');
            }

            $geometryColumn .= $this->getConnection() instanceof PostgresConnection ? '::geometry' : '';
            $query->selectRaw("ST_Distance({$geometryColumn}, ST_GeomFromText(?)) as distance", [
                $this->toWkt($geometry),
            ]);
        }

        return $query;
    }

    public function scopeDistanceSphere($query, $geometryColumn, $geometry, $distance)
    {
        $distFunc = $this->getConnection() instanceof PostgresConnection ? 'ST_DistanceSphere' : 'ST_Distance_Sphere';

        if ($this->isColumnAllowed($geometryColumn)) {
            $geometryColumn .= $this->getConnection() instanceof PostgresConnection ? '::geometry' : '';
            $query->whereRaw("{$distFunc}({$geometryColumn}, ST_GeomFromText(?)) <= ?", [
                $this->toWkt($geometry),
                $distance,
            ]);
        }

        return $query;
    }

    public function scopeDistanceSphereExcludingSelf($query, $geometryColumn, $geometry, $distance)
    {
        $distFunc = $this->getConnection() instanceof PostgresConnection ? 'ST_DistanceSphere' : 'ST_Distance_Sphere';

        if ($this->isColumnAllowed($geometryColumn)) {
            $query = $this->scopeDistanceSphere($query, $geometryColumn, $geometry, $distance);

            $geometryColumn .= $this->getConnection() instanceof PostgresConnection ? '::geometry' : '';
            $query->whereRaw("{$distFunc}({$geometryColumn}, ST_GeomFromText(?)) != 0", [
                $this->toWkt($geometry),
            ]);
        }

        return $query;
    }

    public function scopeDistanceSphereValue($query, $geometryColumn, $geometry)
    {
        $distFunc = $this->getConnection() instanceof PostgresConnection ? 'ST_DistanceSphere' : 'ST_Distance_Sphere';

        if ($this->isColumnAllowed($geometryColumn)) {
            $columns = $query->getQuery()->columns;

            if (!$columns) {
                $query->select('*');
            }

            $geometryColumn .= $this->getConnection() instanceof PostgresConnection ? '::geometry' : '';
            $query->selectRaw("{$distFunc}({$geometryColumn}, ST_GeomFromText(?)) as distance", [
                $this->toWkt($geometry),
            ]);
        }

        return $query;
    }

    public function scopeComparison($query, $geometryColumn, $geometry, $relationship)
    {
        if ($this->isColumnAllowed($geometryColumn)) {
            $relationship = ucfirst(strtolower($relationship));

            if (!in_array($relationship, $this->stRelations)) {
                throw new UnknownSpatialRelationFunction($relationship);
            }

            $geometryColumn .= $this->getConnection() instanceof PostgresConnection ? '::geometry' : '';
            $query->whereRaw("ST_{$relationship}(`{$geometryColumn}`, ST_GeomFromText(?))", [
                $this->toWkt($geometry),
            ]);
        }

        return $query;
    }

    public function scopeWithin($query, $geometryColumn, $polygon)
    {
        return $this->scopeComparison($query, $geometryColumn, $polygon, 'within');
    }

    public function scopeCrosses($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'crosses');
    }

    public function scopeContains($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'contains');
    }

    public function scopeDisjoint($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'disjoint');
    }

    public function scopeEquals($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'equals');
    }

    public function scopeIntersects($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'intersects');
    }

    public function scopeOverlaps($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'overlaps');
    }

    public function scopeDoesTouch($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'touches');
    }
}
