# Laravel Spatial extension

This package is fully untested, undocumented and unstable and is a combination of the two great packages:

-   [Laravel PostGIS extension](https://github.com/njbarrett/laravel-postgis)
-   [Laravel MySQL spatial extension](https://github.com/grimzy/laravel-mysql-spatial)

## Installation

Installation made super-easy with [composer](https://getcomposer.org):

```bash
composer require apptimists/laravel-spatial
```

If you're using a Laravel version before 5.5, also add the `LaravelSpatial\SpatialServiceProvider::class` to your `config/app.php`.

## Requirements

Works with [PostgreSQL](https://www.postgresql.org) installed [PostGIS](http://postgis.net) extension and [MySQL](http://mysql.com) at least version 5.6.

If you try using it on a shared host which is not fulfilling those requirements, change your provider.

## Usage

We use the [GeoJson PHP Library](http://jmikola.github.io/geojson/) for describing spatial fields as GeoJSON object, e.g.:

```php
use GeoJSON\Geometry\Point;

...

$eloquent = new MyModel();
$eloquent->location = new Point([49.7, 6.9]);

...

$eloquent->save();
```
