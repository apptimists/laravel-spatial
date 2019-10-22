# Laravel Spatial extension

This package is fully untested, undocumented and unstable and is a combination of the two great packages:

-   [Laravel PostGIS extension](https://github.com/njbarrett/laravel-postgis)
-   [Laravel MySQL spatial extension](https://github.com/grimzy/laravel-mysql-spatial)

## Installation

Installation made super-easy with [composer](https://getcomposer.org):

```bash
composer require apptimists/laravel-spatial
```

Also add the `LaravelSpatial\SpatialServiceProvider::class` to your `config/app.php`.

## Requirements

Works with [PostgreSQL](https://www.postgresql.org) installed [PostGIS](http://postgis.net) extension and [MySQL](http://mysql.com) at least version 5.6.

If you try using it on a shared host which is not fulfilling those requirements, change your provider.

## Usage

### Migrations

Add spatial fields to your migrations the same way you would any others:

```php
    $table->point('point_column');
    $table->linestring('line_string_column');
    $table->polygon('polygon_column');
    $table->geometry('geometry_column');

    $table->multipoint('multi_point_column');
    $table->multilinestring('multi_line_string_column');
    $table->multipolygon('multi_polygon_column');
    $table->geometrycollection('geometry_collection_column');
```

### Models

Any models that use spatial fields need to use the `LaravelSpatial\Eloquent\SpatialTrait`, and list the spatial fields themselves in the `$spatialFields` property:

```php
use LaravelSpatial\Eloquent\SpatialTrait;

// ...

class MyModel extends Model
{
    use SpatialTrait;

    // ...

    protected $spatialFields = ['location'];

    // ...
}
```

### Values

We use the [GeoJson PHP Library](http://jmikola.github.io/geojson/) for describing spatial fields as GeoJSON object, e.g.:

```php
use GeoJSON\Geometry\Point;

// ...

$eloquent = new MyModel();
$eloquent->location = new Point([49.7, 6.9]);

// ...

$eloquent->save();
```
