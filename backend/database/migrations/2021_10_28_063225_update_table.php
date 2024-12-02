<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('compare_asset_generals', function (Blueprint $table) {
            $table->decimal('total_construction_area', 18, 10)->change();
            $table->decimal('total_area', 18, 10)->change();
        });

        Schema::table('compare_properties', function (Blueprint $table) {
            $table->decimal('asset_general_land_sum_area', 18, 10)->change();
        });

        Schema::table('compare_property_details', function (Blueprint $table) {
            $table->decimal('total_area', 18, 10)->change();
        });
        Schema::table('compare_tangible_assets', function (Blueprint $table) {
            $table->decimal('total_construction_area', 18, 10)->change();
            $table->decimal('total_construction_base', 18, 10)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
