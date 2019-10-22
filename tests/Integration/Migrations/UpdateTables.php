<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelSpatial\MysqlConnection;

class UpdateLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_geometries', function(Blueprint $table) {
            // Make sure point is not nullable
            $table->point('location')->change();

            // The other field changes are just here to test if change works with them, we're not changing anything
            $table->geometry('geo')->default(null)->nullable()->change();
            $table->lineString('line')->default(null)->nullable()->change();
            $table->polygon('shape')->default(null)->nullable()->change();
            $table->multiPoint('multi_locations')->default(null)->nullable()->change();
            $table->multiLineString('multi_lines')->default(null)->nullable()->change();
            $table->multiPolygon('multi_shapes')->default(null)->nullable()->change();
            $table->geometryCollection('multi_geometries')->default(null)->nullable()->change();

        });

        if (is_a(\DB::connection(), MysqlConnection::class)) {
            // MySQL < 5.7.5: table has to be MyISAM
            \DB::statement('ALTER TABLE test_geometries ENGINE = MyISAM');
        }

        Schema::table('test_geometries', function(Blueprint $table) {
            // Add a spatial index on the location field
            $table->spatialIndex('location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_geometries', function(Blueprint $table) {
            $table->dropSpatialIndex(['location']); // either an array of column names or the index name
        });

        if (is_a(\DB::connection(), MysqlConnection::class)) {
            \DB::statement('ALTER TABLE test_geometries ENGINE = InnoDB');
        }

        Schema::table('test_geometries', function(Blueprint $table) {
            $table->point('location')->nullable()->change();
        });
    }
}
