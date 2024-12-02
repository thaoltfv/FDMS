<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFullAddressApartmentAppraiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraises', 'full_address')) {
            Schema::table('appraises', function (Blueprint $table) {
                $table->string('full_address')->nullable();
            });
        }
        if (!Schema::hasColumn('apartment_assets', 'full_address')) {
            Schema::table('apartment_assets', function (Blueprint $table) {
                $table->string('full_address')->nullable();
            });
        }
        if (!Schema::hasColumn('certificate_assets', 'full_address')) {
            Schema::table('certificate_assets', function (Blueprint $table) {
                $table->string('full_address')->nullable();
            });
        }
        if (!Schema::hasColumn('certificate_apartments', 'full_address')) {
            Schema::table('certificate_apartments', function (Blueprint $table) {
                $table->string('full_address')->nullable();
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
        if (Schema::hasColumn('appraises', 'full_address')) {
            Schema::table('appraises', function (Blueprint $table) {
                $table->dropColumn('full_address');
            });
        }
        if (Schema::hasColumn('apartment_assets', 'full_address')) {
            Schema::table('apartment_assets', function (Blueprint $table) {
                $table->dropColumn('full_address');
            });
        }
        if (Schema::hasColumn('certificate_assets', 'full_address')) {
            Schema::table('certificate_assets', function (Blueprint $table) {
                $table->dropColumn('full_address');
            });
        }
        if (Schema::hasColumn('certificate_apartments', 'full_address')) {
            Schema::table('certificate_apartments', function (Blueprint $table) {
                $table->dropColumn('full_address');
            });
        }
    }
}
