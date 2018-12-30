# Laravel Spatial extension

[![](https://img.shields.io/packagist/l/apptimists/laravel-spatial.svg?style=flat-square)](https://packagist.org/packages/apptimists/laravel-spatial)
[![](https://img.shields.io/packagist/php-v/apptimists/laravel-spatial.svg?style=flat-square)](https://packagist.org/packages/apptimists/laravel-spatial)
[![](https://img.shields.io/packagist/v/apptimists/laravel-spatial.svg?style=flat-square)](https://packagist.org/packages/apptimists/laravel-spatial)
[![](https://img.shields.io/packagist/dt/apptimists/laravel-spatial.svg?style=flat-square)](https://packagist.org/packages/apptimists/laravel-spatial)

[![](https://img.shields.io/travis/apptimists/laravel-spatial.svg?style=flat-square)](https://github.com/apptimists/laravel-spatial)
[![](https://img.shields.io/codecov/c/github/apptimists/laravel-spatial.svg?style=flat-square)](https://codecov.io/gh/apptimists/laravel-spatial)
[![](https://img.shields.io/scrutinizer/g/apptimists/laravel-spatial.svg?style=flat-square)](https://scrutinizer-ci.com/g/apptimists/laravel-spatial/)

This package is fully undocumented and unstable, and is a combination of the two great packages:
- [Laravel PostGIS extension](https://github.com/njbarrett/laravel-postgis)
- [Laravel MySQL spatial extension](https://github.com/grimzy/laravel-mysql-spatial)

## Installation

Installation made super-easy with [composer](https://getcomposer.org):
```
composer require apptimists/laravel-spatial
```

Also add the `LaravelSpatial\SpatialServiceProvider::class` to your `config/app.php`.

## Requirements

Works with [PostgreSQL](https://www.postgresql.org) installed [PostGIS](http://postgis.net) extension and [MySQL](http://mysql.com) at least version 5.6.

If you try using it on a shared host which is not fulfilling those requirements, change your provider.

## Usage

We use the [GeoJson PHP Library](http://jmikola.github.io/geojson/) for describing spatial fields as GeoJSON object, e.g.:
```
use GeoJSON\Geometry\Point;

...

$eloquent = new MyModel();
$eloquent->location = new Point([49.7, 6.9]);

...

$eloquent->save();
```
