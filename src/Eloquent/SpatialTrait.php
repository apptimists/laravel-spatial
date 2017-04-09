<?php

namespace LaravelSpatial\Eloquent;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use GeoJson\GeoJson;
use GeoJSON\Geometry\Geometry;
use geoPHP;
use LaravelSpatial\Exceptions\SpatialFieldsNotDefinedException;


trait SpatialTrait
{
    public $geometries = [];

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
                $prefix = "\0\0\0\0";
                if (substr($value, 0, strlen($prefix)) == $prefix) {
                    $value = substr($value, strlen($prefix));
                }

                $geometry = geoPHP::load($value, 'wkb');
                $value = GeoJson::jsonUnserialize(json_decode($geometry->out('json')));
            }
        }

        parent::setRawAttributes($attributes, $sync);
    }

    public function getSpatialFields()
    {
        if (property_exists($this, 'spatialFields')) {
            return $this->spatialFields;
        } else {
            throw new SpatialFieldsNotDefinedException(__CLASS__ . ' has to define $spatialFields');
        }
    }

    protected function performInsert(EloquentBuilder $query, array $options = [])
    {
        foreach ($this->attributes as $key => $value) {
            if ($value instanceof Geometry) {
                $geometry = geoPHP::load((object)$value->jsonSerialize(), 'json');
                $this->geometries[$key] = $value; // Preserve the geometry objects prior to the insert
                $this->attributes[$key] = $this->getConnection()->raw('ST_GeomFromText(\''.$geometry->out('wkt').'\')');
            }
        }

        $insert = parent::performInsert($query, $options);

        foreach ($this->geometries as $key => $value) {
            $this->attributes[$key] = $value; // Retrieve the geometry objects so they can be used in the model
        }

        return $insert; // Return the result of the parent insert
    }
}
