<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailColumnMachineCertificateAssetsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('machine_certificate_assets', 'manufacturer_id')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->integer('manufacturer_id')->nullable()->before('created_at');
            });
        }
        if (!Schema::hasColumn('machine_certificate_assets', 'model')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->string('model')->nullable()->before('created_at');
            });
        }
        if (!Schema::hasColumn('machine_certificate_assets', 'manufacturer_country_id')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->integer('manufacturer_country_id')->nullable()->before('created_at');
            });
        }
        if (!Schema::hasColumn('machine_certificate_assets', 'fuel_id')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->integer('fuel_id')->nullable()->before('created_at');
            });
        }
        if (!Schema::hasColumn('machine_certificate_assets', 'manufacturer_year')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->string('manufacturer_year')->nullable()->before('created_at');
            });
        }
        if (!Schema::hasColumn('machine_certificate_assets', 'using_year')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->string('using_year')->nullable()->before('created_at');
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
        if (Schema::hasColumn('machine_certificate_assets', 'manufacturer_id')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->dropColumn('manufacturer_id');
            });
        }
        if (Schema::hasColumn('machine_certificate_assets', 'model')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->dropColumn('model');
            });
        }
        if (Schema::hasColumn('machine_certificate_assets', 'manufacturer_country_id')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->dropColumn('manufacturer_country_id');
            });
        }
        if (Schema::hasColumn('machine_certificate_assets', 'fuel_id')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->dropColumn('fuel_id');
            });
        }
        if (Schema::hasColumn('machine_certificate_assets', 'manufacturer_year')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->dropColumn('manufacturer_year');
            });
        }
        if (Schema::hasColumn('machine_certificate_assets', 'using_year')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->dropColumn('using_year');
            });
        }
    }
}
