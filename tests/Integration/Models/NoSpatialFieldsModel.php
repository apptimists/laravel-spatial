<?php

use LaravelSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * No Spatial Fields Test Model
 *
 * @property $location
 */
class NoSpatialFieldsModel extends Model
{
    use SpatialTrait;

    protected $table = 'no_spatial_fields';

    public $timestamps = false;
}
