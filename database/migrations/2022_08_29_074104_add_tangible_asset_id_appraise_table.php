<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTangibleAssetIdAppraiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('construction_company', 'tangible_asset_id')) {
            Schema::table('construction_company', function (Blueprint $table) {
				$table->bigInteger('tangible_asset_id')->nullable()->before('created_at');
            });
        }

        if (!Schema::hasColumn('appraise_tangible_comparison_factor', 'tangible_asset_id')) {
            Schema::table('appraise_tangible_comparison_factor', function (Blueprint $table) {
				$table->bigInteger('tangible_asset_id')->nullable()->before('created_at');
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
        if (Schema::hasColumn('construction_company', 'tangible_asset_id')) {
            Schema::table('construction_company', function (Blueprint $table) {
				$table->dropColumn('tangible_asset_id');
            });
        }
        if (Schema::hasColumn('appraise_tangible_comparison_factor', 'tangible_asset_id')) {
            Schema::table('appraise_tangible_comparison_factor', function (Blueprint $table) {
				$table->dropColumn('tangible_asset_id');
            });
        }
    }
}
