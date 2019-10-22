<?php

use LaravelSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Geometry Test Model
 *
 * @property $location
 * @property $line
 * @property $shape
 * @property $geo
 * @property $multi_locations
 * @property $multi_lines
 * @property $multi_shapes
 * @property $multi_geometries
 */
class GeometryModel extends Model
{
    use SpatialTrait;

    protected $table = 'test_geometries';

    protected $spatialFields = [
        'location',
        'line',
        'shape',
        'geo',
        'multi_locations',
        'multi_lines',
        'multi_shapes',
        'multi_geometries',
    ];
}
