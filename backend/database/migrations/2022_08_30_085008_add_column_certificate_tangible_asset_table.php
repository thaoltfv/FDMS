<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCertificateTangibleAssetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificate_asset_tangible_comparison_factor', 'tangible_asset_id')) {
            Schema::table('certificate_asset_tangible_comparison_factor', function (Blueprint $table) {
				$table->bigInteger('tangible_asset_id')->nullable()->before('created_at');
            });
        }
        if (!Schema::hasColumn('certificate_asset_construction_companies', 'tangible_asset_id')) {
            Schema::table('certificate_asset_construction_companies', function (Blueprint $table) {
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
        if (Schema::hasColumn('certificate_asset_tangible_comparison_factor', 'tangible_asset_id')) {
            Schema::table('certificate_asset_tangible_comparison_factor', function (Blueprint $table) {
				$table->dropColumn('tangible_asset_id');
            });
        }
        if (Schema::hasColumn('certificate_asset_construction_companies', 'tangible_asset_id')) {
            Schema::table('certificate_asset_construction_companies', function (Blueprint $table) {
				$table->dropColumn('tangible_asset_id');
            });
        }
    }
}
