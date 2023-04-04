<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraiseTangibleAssetsFourthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (Schema::hasColumn('appraise_tangible_assets', 'contruction_description')) {
			Schema::table('appraise_tangible_assets', function (Blueprint $table) {
				$table->text('contruction_description')->change();
			});
		}
        if (Schema::hasColumn('certificate_asset_tangible_assets', 'contruction_description')) {
			Schema::table('certificate_asset_tangible_assets', function (Blueprint $table) {
				$table->text('contruction_description')->change();
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
