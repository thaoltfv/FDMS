<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraiseUnitAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		// create table appraise_unit_area
		if (!Schema::hasTable('appraise_unit_area')) {
			//DB::statement('create table appraise_unit_area as (select * from appraise_unit_price);');
			DB::statement('CREATE TABLE appraise_unit_area (LIKE appraise_unit_price INCLUDING ALL);');
			DB::statement('INSERT INTO appraise_unit_area SELECT * FROM appraise_unit_price;');
			//DB::statement("SELECT setval('appraise_unit_area_id_seq', (SELECT max(id) FROM appraise_unit_price)+1);");

			if (Schema::hasColumn('appraise_unit_area', 'update')){
				Schema::table('appraise_unit_area', function (Blueprint $table) {
					$table->dropColumn('update');
				});
			}
			if (Schema::hasColumn('appraise_unit_area', 'original_value')){
				Schema::table('appraise_unit_area', function (Blueprint $table) {
					$table->dropColumn('original_value');
				});
			}
			if (Schema::hasColumn('appraise_unit_area', 'update_value')){
				Schema::table('appraise_unit_area', function (Blueprint $table) {
					$table->dropColumn('update_value');
				});
			}
		}
		
		// create table certificate_asset_unit_area
		if (!Schema::hasTable('certificate_asset_unit_area')) {
			//DB::statement('create table certificate_asset_unit_area as (select * from certificate_asset_unit_price);');
			DB::statement('CREATE TABLE certificate_asset_unit_area (LIKE certificate_asset_unit_price INCLUDING ALL);');
			DB::statement('INSERT INTO certificate_asset_unit_area SELECT * FROM certificate_asset_unit_price;');
			//DB::statement("SELECT setval('certificate_asset_unit_area_id_seq', (SELECT max(id) FROM certificate_asset_unit_price)+1);");
			if (Schema::hasColumn('certificate_asset_unit_area', 'update')){
				Schema::table('certificate_asset_unit_area', function (Blueprint $table) {
					$table->dropColumn('update');
				});
			}
			if (Schema::hasColumn('certificate_asset_unit_area', 'original_value')){
				Schema::table('certificate_asset_unit_area', function (Blueprint $table) {
					$table->dropColumn('original_value');
				});
			}
			if (Schema::hasColumn('certificate_asset_unit_area', 'update_value')){
				Schema::table('certificate_asset_unit_area', function (Blueprint $table) {
					$table->dropColumn('update_value');
				});
			}
		} 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appraise_unit_area');
        Schema::dropIfExists('certificate_asset_unit_area');
    }
}
