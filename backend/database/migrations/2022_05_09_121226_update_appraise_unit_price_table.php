<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraiseUnitPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (!Schema::hasColumn('appraise_unit_price', 'violation_asset_area')) {
			Schema::table('appraise_unit_price', function (Blueprint $table) {
				$table->float('violation_asset_area')->default(0)->after('update_value');
			});
		}
		
		if (!Schema::hasColumn('certificate_asset_unit_price', 'violation_asset_area')) {
			Schema::table('certificate_asset_unit_price', function (Blueprint $table) {
				$table->float('violation_asset_area')->default(0)->after('update_value');
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
        //
    }
}
