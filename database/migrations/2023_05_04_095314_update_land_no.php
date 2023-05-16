<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLandNo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('appraise_law_land_details', 'doc_no')) {
            Schema::table('appraise_law_land_details', function (Blueprint $table) {
                $table->string('doc_no')->change();
            });
        }
        if (Schema::hasColumn('appraise_law_land_details', 'land_no')) {
            Schema::table('appraise_law_land_details', function (Blueprint $table) {
                $table->string('land_no')->change();
            });
        }
        if (Schema::hasColumn('certificate_asset_law_land_details', 'doc_no')) {
            Schema::table('certificate_asset_law_land_details', function (Blueprint $table) {
                $table->string('doc_no')->change();
            });
        }
        if (Schema::hasColumn('certificate_asset_law_land_details', 'land_no')) {
            Schema::table('certificate_asset_law_land_details', function (Blueprint $table) {
                $table->string('land_no')->change();
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
        if (Schema::hasColumn('appraise_law_land_details', 'doc_no')) {
            Schema::table('appraise_law_land_details', function (Blueprint $table) {
                $table->integer('doc_no')->change();
            });
        }
        if (Schema::hasColumn('appraise_law_land_details', 'land_no')) {
            Schema::table('appraise_law_land_details', function (Blueprint $table) {
                $table->integer('land_no')->change();
            });
        }
        if (Schema::hasColumn('certificate_asset_law_land_details', 'doc_no')) {
            Schema::table('certificate_asset_law_land_details', function (Blueprint $table) {
                $table->integer('doc_no')->change();
            });
        }
        if (Schema::hasColumn('certificate_asset_law_land_details', 'land_no')) {
            Schema::table('certificate_asset_law_land_details', function (Blueprint $table) {
                $table->integer('land_no')->change();
            });
        }
    }
}
