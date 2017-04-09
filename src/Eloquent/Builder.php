<?php

namespace LaravelSpatial\Eloquent;

use geoPHP;
use GeoJson\Geometry\Geometry;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Builder extends EloquentBuilder
{
    public function update(array $values)
    {
        foreach ($values as $key => &$value) {
            if ($value instanceof Geometry) {
                $geometry = geoPHP::load((object)$value->jsonSerialize(), 'json');
                $value = $this->getQuery()->raw('ST_GeomFromText(\''.$geometry->out('wkt').'\')');
            }
        }

        return parent::update($values);
    }
}
