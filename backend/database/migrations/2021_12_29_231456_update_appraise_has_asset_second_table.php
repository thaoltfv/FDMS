<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraiseHasAssetSecondTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (!Schema::hasColumn('appraise_has_assets', 'asset_price')&&!Schema::hasColumn('appraise_has_assets', 'appraise_price')) {
			Schema::table('appraise_has_assets', function (Blueprint $table) {
				$table->bigInteger('asset_price')->nullable();
				$table->bigInteger('appraise_price')->nullable();
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

    }
}
