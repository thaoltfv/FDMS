<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCertificateComparisionAdjust extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('certificate_apartment_comparison_factors', 'adjust_percent')) {
            Schema::table('certificate_apartment_comparison_factors', function (Blueprint $table) {
                $table->float('adjust_percent')->change();
            });
        }
        if (Schema::hasColumn('certificate_asset_comparison_factor', 'adjust_percent')) {
            Schema::table('certificate_asset_comparison_factor', function (Blueprint $table) {
                $table->float('adjust_percent')->change();
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
        if (Schema::hasColumn('certificate_apartment_comparison_factors', 'adjust_percent')) {
            Schema::table('certificate_apartment_comparison_factors', function (Blueprint $table) {
                $table->integer('adjust_percent')->change();
            });
        }
        if (Schema::hasColumn('certificate_asset_comparison_factor', 'adjust_percent')) {
            Schema::table('certificate_asset_comparison_factor', function (Blueprint $table) {
                $table->integer('adjust_percent')->change();
            });
        }
    }
}
