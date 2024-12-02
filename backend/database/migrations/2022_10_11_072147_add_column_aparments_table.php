<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAparmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('apartments', 'province_id')) {
            Schema::table('apartments', function (Blueprint $table) {
                $table->dropColumn('province_id');
            });
        }
        if (Schema::hasColumn('apartments', 'district_id')) {
            Schema::table('apartments', function (Blueprint $table) {
                $table->dropColumn('district_id');
            });
        }
        if (Schema::hasColumn('apartments', 'ward_id')) {
            Schema::table('apartments', function (Blueprint $table) {
                $table->dropColumn('ward_id');
            });
        }
        if (Schema::hasColumn('apartments', 'street_id')) {
            Schema::table('apartments', function (Blueprint $table) {
                $table->dropColumn('street_id');
            });
        }
        if (Schema::hasColumn('apartments', 'coordinates')) {
            Schema::table('apartments', function (Blueprint $table) {
                $table->dropColumn('coordinates');
            });
        }
        if (Schema::hasColumn('apartments', 'address')) {
            Schema::table('apartments', function (Blueprint $table) {
                $table->dropColumn('address');
            });
        }

        if (!Schema::hasColumn('apartments', 'name')) {
            Schema::table('apartments', function (Blueprint $table) {
                $table->string('name');
            });
        }
        if (!Schema::hasColumn('apartments', 'floor_id')) {
            Schema::table('apartments', function (Blueprint $table) {
                $table->integer('floor_id')->nullable();
            });
        }
        if (!Schema::hasColumn('apartments', 'status')) {
            Schema::table('apartments', function (Blueprint $table) {
                $table->boolean('status')->nullable();
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
