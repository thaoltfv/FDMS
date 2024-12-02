<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameToApartmentAssetPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('apartment_asset_properties', 'full_name')) {
            Schema::table('apartment_asset_properties', function (Blueprint $table) {
                $table->string('full_name')->nullable();
            });
        }
        if (!Schema::hasColumn('certificate_apartment_properties', 'full_name')) {
            Schema::table('certificate_apartment_properties', function (Blueprint $table) {
                $table->string('full_name')->nullable();
            });
        }
        if (!Schema::hasColumn('certificate_apartment_properties', 'apartment_name')) {
            Schema::table('certificate_apartment_properties', function (Blueprint $table) {
                $table->string('apartment_name')->nullable();
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
        if (Schema::hasColumn('apartment_asset_properties', 'full_name')) {
            Schema::table('apartment_asset_properties', function (Blueprint $table) {
                $table->dropColumn('full_name');
            });
        }
        if (Schema::hasColumn('certificate_apartment_properties', 'full_name')) {
            Schema::table('certificate_apartment_properties', function (Blueprint $table) {
                $table->dropColumn('full_name');
            });
        }
        if (Schema::hasColumn('certificate_apartment_properties', 'apartment_name')) {
            Schema::table('certificate_apartment_properties', function (Blueprint $table) {
                $table->dropColumn('apartment_name');
            });
        }
    }
}
