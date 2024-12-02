<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSubStatusPersonalPropertyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('apartment_assets', 'sub_status')) {
            Schema::table('apartment_assets', function (Blueprint $table) {
                $table->integer('sub_status')->default(1);
            });
        }
        if (! Schema::hasColumn('personal_properties', 'sub_status')) {
            Schema::table('personal_properties', function (Blueprint $table) {
                $table->integer('sub_status')->default(1);
            });
        }
        if (! Schema::hasColumn('other_certificate_assets', 'sub_status')) {
            Schema::table('other_certificate_assets', function (Blueprint $table) {
                $table->integer('sub_status')->default(1);
            });
        }
        if (! Schema::hasColumn('machine_certificate_assets', 'sub_status')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->integer('sub_status')->default(1);
            });
        }
        if (! Schema::hasColumn('verhicle_certificate_assets', 'sub_status')) {
            Schema::table('verhicle_certificate_assets', function (Blueprint $table) {
                $table->integer('sub_status')->default(1);
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
        if (Schema::hasColumn('apartment_assets', 'sub_status')) {
            Schema::table('apartment_assets', function (Blueprint $table) {
                $table->dropColumn('sub_status');
            });
        }
        if (Schema::hasColumn('personal_properties', 'sub_status')) {
            Schema::table('personal_properties', function (Blueprint $table) {
                $table->dropColumn('sub_status');
            });
        }
        if (Schema::hasColumn('other_certificate_assets', 'sub_status')) {
            Schema::table('other_certificate_assets', function (Blueprint $table) {
                $table->dropColumn('sub_status');
            });
        }
        if (Schema::hasColumn('machine_certificate_assets', 'sub_status')) {
            Schema::table('machine_certificate_assets', function (Blueprint $table) {
                $table->dropColumn('sub_status');
            });
        }
        if (Schema::hasColumn('verhicle_certificate_assets', 'sub_status')) {
            Schema::table('verhicle_certificate_assets', function (Blueprint $table) {
                $table->dropColumn('sub_status');
            });
        }
    }
}
