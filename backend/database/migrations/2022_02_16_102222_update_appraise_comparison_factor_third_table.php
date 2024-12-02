<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraiseComparisonFactorThirdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (!Schema::hasColumn('appraise_comparison_factor', 'position')) {
			Schema::table('appraise_comparison_factor', function (Blueprint $table) {
				$table->integer('position')->default(0);
			});
		}
        if (!Schema::hasColumn('certificate_asset_comparison_factor', 'position')) {
			Schema::table('certificate_asset_comparison_factor', function (Blueprint $table) {
				$table->integer('position')->default(0);
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
