<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCertificateAssetSecondTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (Schema::hasColumn('certificate_assets', 'land_no')) {
			Schema::table('certificate_assets', function (Blueprint $table) {
				$table->string('land_no')->nullable()->change();
			});
		}
        if (Schema::hasColumn('certificate_assets', 'doc_no')) {
			Schema::table('certificate_assets', function (Blueprint $table) {
				$table->string('doc_no')->nullable()->change();
			});
		}
		
        if (Schema::hasColumn('certificate_asset_law', 'land_no')) {
			Schema::table('certificate_asset_law', function (Blueprint $table) {
				$table->string('land_no')->nullable()->change();
			});
		}
        if (Schema::hasColumn('certificate_asset_law', 'doc_no')) {
			Schema::table('certificate_asset_law', function (Blueprint $table) {
				$table->string('doc_no')->nullable()->change();
			});
		}
		
		if (Schema::hasColumn('certificate_asset_properties', 'land_no')) {
			Schema::table('certificate_asset_properties', function (Blueprint $table) {
				$table->string('land_no')->nullable()->change();
			});
		}
        if (Schema::hasColumn('certificate_asset_properties', 'doc_no')) {
			Schema::table('certificate_asset_properties', function (Blueprint $table) {
				$table->string('doc_no')->nullable()->change();
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
