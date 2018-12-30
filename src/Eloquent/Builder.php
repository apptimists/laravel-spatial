<?php

namespace LaravelSpatial\Eloquent;

use GeoJson\Geometry\Geometry;
use geoPHP\geoPHP;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Builder extends EloquentBuilder
{
    public function update(array $values)
    {
        foreach ($values as $key => &$value) {
            if ($value instanceof Geometry) {
                $wkt = geoPHP::load(json_decode(json_encode($value->jsonSerialize()), false), 'json')->out('wkt');
                $value = $this->getQuery()->raw("ST_GeomFromText('{$wkt}')");
            }
        }

        return parent::update($values);
    }
}
