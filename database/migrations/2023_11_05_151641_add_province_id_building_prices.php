<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProvinceIdBuildingPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('building_prices', 'province_id')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->integer('province_id')->nullable()->before('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('building_prices', 'province_id')){
            Schema::table('building_prices', function (Blueprint $table) {
                $table->dropColumn('province_id');
            });
        }
    }
}
